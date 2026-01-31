<?php
/**
 * Moderation Handler
 * Maneja reportes y acciones de moderación
 */
class FR_Moderation {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Crear un reporte
     */
    public function report($reporter_id, $content_type, $content_id, $reason, $description = '') {
        // Validar tipo de contenido
        if (!in_array($content_type, ['topic', 'reply'])) {
            return false;
        }
        
        // Obtener el usuario reportado
        $reported_user_id = $this->get_content_author($content_type, $content_id);
        
        if (!$reported_user_id) {
            return false;
        }
        
        // Verificar que no se reporte a sí mismo
        if ($reporter_id == $reported_user_id) {
            return false;
        }
        
        // Verificar si ya existe un reporte pendiente para este contenido
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('reports');
        
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} 
             WHERE content_type = %s 
             AND content_id = %d 
             AND reporter_id = %d 
             AND status = 'pending'",
            $content_type,
            $content_id,
            $reporter_id
        ));
        
        if ($existing) {
            return false; // Ya existe un reporte
        }
        
        // Insertar el reporte
        $result = $this->db->insert('reports', [
            'reporter_id' => $reporter_id,
            'reported_user_id' => $reported_user_id,
            'content_type' => $content_type,
            'content_id' => $content_id,
            'reason' => $reason,
            'description' => sanitize_textarea_field($description)
        ]);
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Obtener autor del contenido
     */
    private function get_content_author($type, $id) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table($type === 'topic' ? 'topics' : 'replies');
        
        return $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$table} WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Obtener reportes pendientes (para moderadores)
     */
    public function get_pending_reports($page = 1, $per_page = 20) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('reports');
        $offset = ($page - 1) * $per_page;
        
        $reports = $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, 
                    reporter.display_name as reporter_name,
                    reported.display_name as reported_name
             FROM {$table} r
             LEFT JOIN {$wpdb->users} reporter ON r.reporter_id = reporter.ID
             LEFT JOIN {$wpdb->users} reported ON r.reported_user_id = reported.ID
             WHERE r.status = 'pending'
             ORDER BY r.created_at DESC
             LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ));
        
        // Añadir el contenido reportado
        foreach ($reports as &$report) {
            $report->content = $this->get_reported_content($report->content_type, $report->content_id);
        }
        
        return $reports;
    }
    
    /**
     * Obtener el contenido reportado
     */
    private function get_reported_content($type, $id) {
        $wpdb = $this->db->get_wpdb();
        
        if ($type === 'topic') {
            $table = $this->db->get_table('topics');
            return $wpdb->get_row($wpdb->prepare(
                "SELECT title, content, slug FROM {$table} WHERE id = %d",
                $id
            ));
        } else {
            $table = $this->db->get_table('replies');
            return $wpdb->get_row($wpdb->prepare(
                "SELECT content FROM {$table} WHERE id = %d",
                $id
            ));
        }
    }
    
    /**
     * Revisar un reporte (acción de moderador)
     */
    public function review_report($report_id, $action, $moderator_id) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('reports');
        
        // Obtener el reporte
        $report = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE id = %d",
            $report_id
        ));
        
        if (!$report || $report->status !== 'pending') {
            return false;
        }
        
        // Acciones posibles: dismiss (ignorar), warn (advertir), delete (eliminar contenido), ban (banear usuario)
        switch ($action) {
            case 'dismiss':
                // Solo marcar como revisado
                break;
                
            case 'delete':
                // Eliminar el contenido
                $this->delete_content($report->content_type, $report->content_id);
                // Notificar al usuario
                $this->notify_user_moderation($report->reported_user_id, 'content_deleted', $report->content_type);
                break;
                
            case 'warn':
                // Enviar advertencia
                $this->notify_user_moderation($report->reported_user_id, 'warning', $report->content_type);
                break;
                
            case 'ban':
                // Banear usuario (marcar en usermeta)
                update_user_meta($report->reported_user_id, 'fr_banned', true);
                update_user_meta($report->reported_user_id, 'fr_banned_at', current_time('mysql'));
                update_user_meta($report->reported_user_id, 'fr_banned_by', $moderator_id);
                $this->notify_user_moderation($report->reported_user_id, 'banned', '');
                break;
        }
        
        // Actualizar estado del reporte
        return $this->db->update('reports', [
            'status' => 'reviewed',
            'reviewed_by' => $moderator_id,
            'reviewed_at' => current_time('mysql')
        ], ['id' => $report_id]);
    }
    
    /**
     * Eliminar contenido
     */
    private function delete_content($type, $id) {
        $table = $type === 'topic' ? 'topics' : 'replies';
        return $this->db->update($table, ['status' => 'trash'], ['id' => $id]);
    }
    
    /**
     * Notificar al usuario sobre acción de moderación
     */
    private function notify_user_moderation($user_id, $action_type, $content_type) {
        $notification = new FR_Notification();
        
        $messages = [
            'content_deleted' => 'Tu ' . ($content_type === 'topic' ? 'tema' : 'respuesta') . ' fue eliminado por violar las normas.',
            'warning' => 'Has recibido una advertencia por contenido inapropiado.',
            'banned' => 'Tu cuenta ha sido suspendida por violar las normas de la comunidad.'
        ];
        
        $notification->create($user_id, 'moderation', $messages[$action_type], home_url('/'));
    }
    
    /**
     * Verificar si usuario está baneado
     */
    public function is_user_banned($user_id) {
        return get_user_meta($user_id, 'fr_banned', true) === true;
    }
    
    /**
     * Contar reportes pendientes
     */
    public function count_pending() {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('reports');
        
        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table} WHERE status = 'pending'"
        );
    }
    
    /**
     * Obtener razones de reporte
     */
    public static function get_report_reasons() {
        return [
            'spam' => 'Spam o publicidad',
            'offensive' => 'Contenido ofensivo',
            'misinformation' => 'Información falsa o peligrosa',
            'other' => 'Otro motivo'
        ];
    }
}
