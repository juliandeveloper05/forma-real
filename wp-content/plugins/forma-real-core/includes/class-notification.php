<?php
/**
 * Notification Handler
 * Maneja el sistema de notificaciones
 */
class FR_Notification {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Crear una notificación
     */
    public function create($user_id, $type, $content, $link = '') {
        if (!in_array($type, ['reply', 'mention', 'moderation', 'system'])) {
            return false;
        }
        
        return $this->db->insert('notifications', [
            'user_id' => $user_id,
            'type' => $type,
            'content' => sanitize_text_field($content),
            'link' => esc_url($link)
        ]);
    }
    
    /**
     * Obtener notificaciones no leídas
     */
    public function get_unread($user_id, $limit = 10) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('notifications');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} 
             WHERE user_id = %d AND is_read = 0 
             ORDER BY created_at DESC 
             LIMIT %d",
            $user_id,
            $limit
        ));
    }
    
    /**
     * Obtener todas las notificaciones
     */
    public function get_all($user_id, $page = 1, $per_page = 20) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('notifications');
        $offset = ($page - 1) * $per_page;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} 
             WHERE user_id = %d 
             ORDER BY created_at DESC 
             LIMIT %d OFFSET %d",
            $user_id,
            $per_page,
            $offset
        ));
    }
    
    /**
     * Marcar notificación como leída
     */
    public function mark_as_read($notification_id) {
        return $this->db->update('notifications', 
            ['is_read' => 1], 
            ['id' => $notification_id]
        );
    }
    
    /**
     * Marcar todas como leídas
     */
    public function mark_all_as_read($user_id) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('notifications');
        
        return $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET is_read = 1 WHERE user_id = %d AND is_read = 0",
            $user_id
        ));
    }
    
    /**
     * Contar notificaciones no leídas
     */
    public function get_unread_count($user_id) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('notifications');
        
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND is_read = 0",
            $user_id
        ));
    }
    
    /**
     * Eliminar notificaciones antiguas (más de 30 días)
     */
    public function cleanup_old($days = 30) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('notifications');
        
        return $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
    }
    
    /**
     * Notificar al autor de un tema cuando recibe respuesta
     */
    public function notify_topic_reply($topic_id, $replier_id) {
        $wpdb = $this->db->get_wpdb();
        $topics_table = $this->db->get_table('topics');
        $forums_table = $this->db->get_table('forums');
        
        // Obtener info del tema
        $topic = $wpdb->get_row($wpdb->prepare(
            "SELECT t.*, f.slug as forum_slug 
             FROM {$topics_table} t
             LEFT JOIN {$forums_table} f ON t.forum_id = f.id
             WHERE t.id = %d",
            $topic_id
        ));
        
        if (!$topic || $topic->user_id == $replier_id) {
            return false; // No notificar si se responde uno mismo
        }
        
        $replier = get_userdata($replier_id);
        $replier_name = $replier ? $replier->display_name : 'Alguien';
        
        $link = home_url("/foro/{$topic->forum_slug}/{$topic->slug}");
        $content = "{$replier_name} respondió a tu tema \"{$topic->title}\"";
        
        return $this->create($topic->user_id, 'reply', $content, $link);
    }
    
    /**
     * Obtener icono según tipo de notificación
     */
    public static function get_icon($type) {
        $icons = [
            'reply' => '💬',
            'mention' => '@',
            'moderation' => '⚠️',
            'system' => 'ℹ️'
        ];
        return $icons[$type] ?? '🔔';
    }
}
