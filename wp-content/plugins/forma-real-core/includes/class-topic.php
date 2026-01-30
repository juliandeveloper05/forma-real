<?php
/**
 * Topic Handler
 * Maneja operaciones de topics del foro
 */
class FR_Topic {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Crear nuevo topic
     */
    public function create($data) {
        // Validar datos
        if (!$this->validate_topic_data($data)) {
            return new WP_Error('invalid_data', 'Datos inválidos');
        }
        
        // Sanitizar
        $sanitized = $this->sanitize_topic_data($data);
        
        // Generar slug único
        $sanitized['slug'] = $this->generate_unique_slug($sanitized['title']);
        
        // Insertar
        $result = $this->db->insert('topics', $sanitized);
        
        if ($result) {
            $topic_id = $this->db->get_wpdb()->insert_id;
            
            // Actualizar contador del foro
            $forum = new FR_Forum();
            $forum->update_topic_count($sanitized['forum_id']);
            
            // Registrar actividad
            $this->log_activity($sanitized['user_id'], 'topic_created', $topic_id);
            
            return $topic_id;
        }
        
        return false;
    }
    
    /**
     * Obtener topics recientes
     */
    public function get_recent_topics($limit = 20, $offset = 0) {
        global $wpdb;
        $table = $this->db->get_table('topics');
        $forums_table = $this->db->get_table('forums');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT t.*, u.display_name as author_name, f.name as forum_name
             FROM {$table} t
             LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID
             LEFT JOIN {$forums_table} f ON t.forum_id = f.id
             WHERE t.status = 'approved'
             ORDER BY t.is_sticky DESC, t.last_active_time DESC
             LIMIT %d OFFSET %d",
            $limit, $offset
        ));
    }
    
    /**
     * Obtener topic por ID con datos relacionados
     */
    public function get_topic_full($topic_id) {
        global $wpdb;
        $topics_table = $this->db->get_table('topics');
        $forums_table = $this->db->get_table('forums');
        $profiles_table = $this->db->get_table('profiles');
        
        $topic = $wpdb->get_row($wpdb->prepare(
            "SELECT t.*, 
                    u.display_name as author_name,
                    u.user_email as author_email,
                    f.name as forum_name,
                    f.slug as forum_slug,
                    p.fitness_level as author_level
             FROM {$topics_table} t
             LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID
             LEFT JOIN {$forums_table} f ON t.forum_id = f.id
             LEFT JOIN {$profiles_table} p ON t.user_id = p.user_id
             WHERE t.id = %d",
            $topic_id
        ));
        
        if ($topic) {
            // Incrementar contador de vistas
            $this->increment_view_count($topic_id);
        }
        
        return $topic;
    }
    
    /**
     * Buscar topics (fulltext search)
     */
    public function search($query) {
        global $wpdb;
        $table = $this->db->get_table('topics');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT t.*, 
                    MATCH(t.title, t.content) AGAINST(%s) as relevance
             FROM {$table} t
             WHERE MATCH(t.title, t.content) AGAINST(%s IN BOOLEAN MODE)
             AND t.status = 'approved'
             ORDER BY relevance DESC
             LIMIT 50",
            $query, $query
        ));
    }
    
    private function validate_topic_data($data) {
        return (
            !empty($data['title']) &&
            !empty($data['content']) &&
            !empty($data['forum_id']) &&
            !empty($data['user_id'])
        );
    }
    
    private function sanitize_topic_data($data) {
        return [
            'forum_id' => absint($data['forum_id']),
            'user_id' => absint($data['user_id']),
            'title' => sanitize_text_field($data['title']),
            'content' => wp_kses_post($data['content']),
            'status' => isset($data['status']) ? $data['status'] : 'approved'
        ];
    }
    
    private function generate_unique_slug($title) {
        $slug = sanitize_title($title);
        $original_slug = $slug;
        $counter = 1;
        
        global $wpdb;
        $table = $this->db->get_table('topics');
        
        while ($wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE slug = %s",
            $slug
        ))) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function increment_view_count($topic_id) {
        global $wpdb;
        $table = $this->db->get_table('topics');
        
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET view_count = view_count + 1 WHERE id = %d",
            $topic_id
        ));
    }
    
    private function log_activity($user_id, $action, $content_id) {
        $this->db->insert('user_activity', [
            'user_id' => $user_id,
            'action_type' => $action,
            'content_type' => 'topic',
            'content_id' => $content_id
        ]);
    }
}
