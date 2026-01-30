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
        
        // Hooks para usuarios no logueados (si fuera necesario, por ahora no)
        // add_action('wp_ajax_nopriv_fr_create_topic', [$this, 'handle_unauthorized']);
    }
    
    /**
     * Manejar creación de topic
     */
    public function handle_create_topic() {
        check_ajax_referer('fr_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Debes iniciar sesión.']);
        }
        
        $data = [
            'title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'content' => isset($_POST['content']) ? wp_kses_post($_POST['content']) : '',
            'forum_id' => isset($_POST['forum_id']) ? intval($_POST['forum_id']) : 0,
            'user_id' => get_current_user_id(),
            'status' => 'approved' // En el futuro podría depender de roles
        ];
        
        $topic_handler = new FR_Topic();
        $topic_id = $topic_handler->create($data);
        
        if ($topic_id) {
            wp_send_json_success([
                'message' => 'Tema creado con éxito',
                'redirect_url' => get_permalink($topic_id) // Esto requerirá integración con rewrite rules
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
        
        $data = [
            'topic_id' => isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0,
            'content' => isset($_POST['content']) ? wp_kses_post($_POST['content']) : '',
            'user_id' => get_current_user_id()
        ];
        
        $reply_handler = new FR_Reply();
        $reply_id = $reply_handler->create($data);
        
        if ($reply_id) {
            wp_send_json_success([
                'message' => 'Respuesta publicada',
                'reply_html' => '<!-- HTML de la respuesta, se implementará luego -->'
            ]);
        } else {
            wp_send_json_error(['message' => 'Error al publicar la respuesta.']);
        }
    }
}
