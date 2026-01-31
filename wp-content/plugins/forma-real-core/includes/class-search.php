<?php
/**
 * Search Handler
 * Maneja la búsqueda FULLTEXT de temas y respuestas
 */
class FR_Search {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Búsqueda general (temas + respuestas)
     */
    public function search($query, $page = 1, $per_page = 10) {
        $query = sanitize_text_field($query);
        
        if (strlen($query) < 3) {
            return ['results' => [], 'total' => 0, 'message' => 'La búsqueda debe tener al menos 3 caracteres.'];
        }
        
        $topics = $this->search_topics($query);
        $replies = $this->search_replies($query);
        
        // Combinar y ordenar por relevancia (score)
        $results = array_merge($topics, $replies);
        usort($results, function($a, $b) {
            return $b->score <=> $a->score;
        });
        
        $total = count($results);
        $offset = ($page - 1) * $per_page;
        $paginated = array_slice($results, $offset, $per_page);
        
        return [
            'results' => $paginated,
            'total' => $total,
            'pages' => ceil($total / $per_page),
            'current_page' => $page
        ];
    }
    
    /**
     * Buscar solo en temas
     */
    public function search_topics($query) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('topics');
        $forums_table = $this->db->get_table('forums');
        
        $query = esc_sql($query);
        
        $sql = $wpdb->prepare(
            "SELECT t.*, 
                    f.name as forum_name, 
                    f.slug as forum_slug,
                    u.display_name as author_name,
                    MATCH(t.title, t.content) AGAINST(%s) as score,
                    'topic' as result_type
             FROM {$table} t
             LEFT JOIN {$forums_table} f ON t.forum_id = f.id
             LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID
             WHERE t.status = 'approved'
             AND MATCH(t.title, t.content) AGAINST(%s IN NATURAL LANGUAGE MODE)
             ORDER BY score DESC
             LIMIT 50",
            $query,
            $query
        );
        
        $results = $wpdb->get_results($sql);
        
        return $results ?: [];
    }
    
    /**
     * Buscar solo en respuestas
     */
    public function search_replies($query) {
        $wpdb = $this->db->get_wpdb();
        $replies_table = $this->db->get_table('replies');
        $topics_table = $this->db->get_table('topics');
        $forums_table = $this->db->get_table('forums');
        
        $query = esc_sql($query);
        
        $sql = $wpdb->prepare(
            "SELECT r.*, 
                    t.title as topic_title,
                    t.slug as topic_slug,
                    f.slug as forum_slug,
                    u.display_name as author_name,
                    MATCH(r.content) AGAINST(%s) as score,
                    'reply' as result_type
             FROM {$replies_table} r
             LEFT JOIN {$topics_table} t ON r.topic_id = t.id
             LEFT JOIN {$forums_table} f ON t.forum_id = f.id
             LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
             WHERE r.status = 'approved'
             AND MATCH(r.content) AGAINST(%s IN NATURAL LANGUAGE MODE)
             ORDER BY score DESC
             LIMIT 50",
            $query,
            $query
        );
        
        $results = $wpdb->get_results($sql);
        
        return $results ?: [];
    }
    
    /**
     * Sugerencias de búsqueda (autocomplete)
     */
    public function get_suggestions($query, $limit = 5) {
        $wpdb = $this->db->get_wpdb();
        $table = $this->db->get_table('topics');
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT title FROM {$table} 
             WHERE status = 'approved' 
             AND title LIKE %s 
             LIMIT %d",
            '%' . $wpdb->esc_like($query) . '%',
            $limit
        ));
        
        return array_map(function($r) { return $r->title; }, $results);
    }
}
