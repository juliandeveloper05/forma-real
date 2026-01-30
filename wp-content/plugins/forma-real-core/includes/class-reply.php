<?php
/**
 * Reply Handler
 * Maneja operaciones de respuestas
 */
class FR_Reply {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Crear nueva respuesta
     */
    public function create($data) {
        if (!$this->validate_reply_data($data)) {
            return new WP_Error('invalid_data', 'Datos inválidos');
        }
        
        $sanitized = $this->sanitize_reply_data($data);
        
        $result = $this->db->insert('replies', $sanitized);
        
        if ($result) {
            $reply_id = $this->db->get_wpdb()->insert_id;
            
            // Actualizar último reply en topic
            $this->update_topic_last_reply($sanitized['topic_id'], $reply_id);
            
            // Notificar al autor del topic
            $this->notify_topic_author($sanitized['topic_id'], $sanitized['user_id']);
            
            return $reply_id;
        }
        
        return false;
    }
    
    /**
     * Obtener respuestas de un topic
     */
    public function get_by_topic($topic_id, $limit = 15, $offset = 0) {
        global $wpdb;
        $table = $this->db->get_table('replies');
        $profiles_table = $this->db->get_table('profiles');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT r.*, 
                    u.display_name as author_name,
                    p.fitness_level as author_level
             FROM {$table} r
             LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
             LEFT JOIN {$profiles_table} p ON r.user_id = p.user_id
             WHERE r.topic_id = %d 
             AND r.status = 'approved'
             ORDER BY r.created_at ASC
             LIMIT %d OFFSET %d",
            $topic_id, $limit, $offset
        ));
    }
    
    private function validate_reply_data($data) {
        return (
            !empty($data['content']) &&
            !empty($data['topic_id']) &&
            !empty($data['user_id'])
        );
    }
    
    private function sanitize_reply_data($data) {
        return [
            'topic_id' => absint($data['topic_id']),
            'user_id' => absint($data['user_id']),
            'parent_id' => isset($data['parent_id']) ? absint($data['parent_id']) : null,
            'content' => wp_kses_post($data['content']),
            'status' => isset($data['status']) ? $data['status'] : 'approved'
        ];
    }
    
    private function update_topic_last_reply($topic_id, $reply_id) {
        $this->db->update('topics', 
            [
                'last_reply_id' => $reply_id,
                'last_active_time' => current_time('mysql'),
                'reply_count' => $this->get_reply_count($topic_id) + 1
            ],
            ['id' => $topic_id]
        );
    }

    private function get_reply_count($topic_id) {
        global $wpdb;
        $table = $this->db->get_table('replies');
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE topic_id = %d AND status = 'approved'",
            $topic_id
        ));
    }
    
    private function notify_topic_author($topic_id, $reply_author_id) {
        global $wpdb;
        $topics_table = $this->db->get_table('topics');
        
        $topic_author_id = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$topics_table} WHERE id = %d",
            $topic_id
        ));
        
        // No notificar si el autor responde su propio topic
        if ($topic_author_id == $reply_author_id) {
            return;
        }
        
        $this->db->insert('notifications', [
            'user_id' => $topic_author_id,
            'type' => 'reply',
            'content' => 'Hay una nueva respuesta en tu topic',
            'link' => home_url('/topic/' . $topic_id) // Placeholder URL
        ]);
    }
}
