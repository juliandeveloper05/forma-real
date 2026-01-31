<?php
/**
 * AJAX Handler
 * Maneja las peticiones AJAX del frontend
 */
class FR_Ajax_Handler {
    
    public function __construct() {
        // Hooks para usuarios logueados
        add_action('wp_ajax_fr_create_topic', [$this, 'handle_create_topic']);
        add_action('wp_ajax_fr_create_reply', [$this, 'handle_create_reply']);
        add_action('wp_ajax_fr_search', [$this, 'handle_search']);
        add_action('wp_ajax_nopriv_fr_search', [$this, 'handle_search']); // Búsqueda pública
        add_action('wp_ajax_fr_report_content', [$this, 'handle_report_content']);
        add_action('wp_ajax_fr_get_notifications', [$this, 'handle_get_notifications']);
        add_action('wp_ajax_fr_mark_notification_read', [$this, 'handle_mark_notification_read']);
        add_action('wp_ajax_fr_mark_all_notifications_read', [$this, 'handle_mark_all_notifications_read']);
        add_action('wp_ajax_fr_review_report', [$this, 'handle_review_report']);
    }
    
    /**
     * Manejar creación de topic
     */
    public function handle_create_topic() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Debes iniciar sesión.']);
        }
        
        // Verificar si usuario está baneado
        $moderation = new FR_Moderation();
        if ($moderation->is_user_banned(get_current_user_id())) {
            wp_send_json_error(['message' => 'Tu cuenta está suspendida.']);
        }
        
        $data = [
            'title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'content' => isset($_POST['content']) ? wp_kses_post($_POST['content']) : '',
            'forum_id' => isset($_POST['forum_id']) ? intval($_POST['forum_id']) : 0,
            'user_id' => get_current_user_id(),
            'status' => 'approved'
        ];
        
        $topic_handler = new FR_Topic();
        $topic_id = $topic_handler->create($data);
        
        if ($topic_id) {
            wp_send_json_success([
                'message' => 'Tema creado con éxito',
                'redirect_url' => get_permalink($topic_id)
            ]);
        } else {
            wp_send_json_error(['message' => 'Error al crear el tema.']);
        }
    }
    
    /**
     * Manejar creación de respuesta
     */
    public function handle_create_reply() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Debes iniciar sesión.']);
        }
        
        // Verificar si usuario está baneado
        $moderation = new FR_Moderation();
        if ($moderation->is_user_banned(get_current_user_id())) {
            wp_send_json_error(['message' => 'Tu cuenta está suspendida.']);
        }
        
        $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
        $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
        $user_id = get_current_user_id();
        
        $data = [
            'topic_id' => $topic_id,
            'content' => $content,
            'user_id' => $user_id
        ];
        
        $reply_handler = new FR_Reply();
        $reply_id = $reply_handler->create($data);
        
        if ($reply_id) {
            // Crear notificación para el autor del tema
            $notification = new FR_Notification();
            $notification->notify_topic_reply($topic_id, $user_id);
            
            wp_send_json_success([
                'message' => 'Respuesta publicada',
                'reply_id' => $reply_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Error al publicar la respuesta.']);
        }
    }
    
    /**
     * Manejar búsqueda
     */
    public function handle_search() {
        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        
        if (strlen($query) < 3) {
            wp_send_json_error(['message' => 'La búsqueda debe tener al menos 3 caracteres.']);
        }
        
        $search = new FR_Search();
        $results = $search->search($query, $page);
        
        wp_send_json_success($results);
    }
    
    /**
     * Manejar reporte de contenido
     */
    public function handle_report_content() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Debes iniciar sesión para reportar.']);
        }
        
        $content_type = isset($_POST['content_type']) ? sanitize_text_field($_POST['content_type']) : '';
        $content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
        $reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        
        $moderation = new FR_Moderation();
        $report_id = $moderation->report(
            get_current_user_id(),
            $content_type,
            $content_id,
            $reason,
            $description
        );
        
        if ($report_id) {
            wp_send_json_success(['message' => 'Reporte enviado. Gracias por ayudar a mantener la comunidad.']);
        } else {
            wp_send_json_error(['message' => 'No se pudo enviar el reporte. Puede que ya lo hayas reportado.']);
        }
    }
    
    /**
     * Obtener notificaciones del usuario
     */
    public function handle_get_notifications() {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'No autenticado.']);
        }
        
        $notification = new FR_Notification();
        $unread = $notification->get_unread(get_current_user_id());
        $count = $notification->get_unread_count(get_current_user_id());
        
        wp_send_json_success([
            'notifications' => $unread,
            'unread_count' => $count
        ]);
    }
    
    /**
     * Marcar notificación como leída
     */
    public function handle_mark_notification_read() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'No autenticado.']);
        }
        
        $notification_id = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : 0;
        
        $notification = new FR_Notification();
        $notification->mark_as_read($notification_id);
        
        wp_send_json_success(['message' => 'Notificación marcada como leída.']);
    }
    
    /**
     * Marcar todas las notificaciones como leídas
     */
    public function handle_mark_all_notifications_read() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'No autenticado.']);
        }
        
        $notification = new FR_Notification();
        $notification->mark_all_as_read(get_current_user_id());
        
        wp_send_json_success(['message' => 'Todas las notificaciones marcadas como leídas.']);
    }
    
    /**
     * Revisar reporte (solo moderadores)
     */
    public function handle_review_report() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!current_user_can('moderate_comments')) {
            wp_send_json_error(['message' => 'No tienes permisos de moderador.']);
        }
        
        $report_id = isset($_POST['report_id']) ? intval($_POST['report_id']) : 0;
        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        
        if (!in_array($action, ['dismiss', 'warn', 'delete', 'ban'])) {
            wp_send_json_error(['message' => 'Acción no válida.']);
        }
        
        $moderation = new FR_Moderation();
        $result = $moderation->review_report($report_id, $action, get_current_user_id());
        
        if ($result) {
            wp_send_json_success(['message' => 'Reporte procesado correctamente.']);
        } else {
            wp_send_json_error(['message' => 'Error al procesar el reporte.']);
        }
    }
}
