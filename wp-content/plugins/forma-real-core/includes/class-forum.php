<?php
/**
 * Forum Handler
 * Maneja operaciones de categorías del foro
 */
class FR_Forum {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Obtener todos los foros principales
     */
    public function get_all_forums() {
        global $wpdb;
        $table = $this->db->get_table('forums');
        
        return $wpdb->get_results(
            "SELECT * FROM {$table} 
             WHERE parent_id IS NULL 
             AND is_active = 1 
             ORDER BY display_order ASC"
        );
    }
    
    /**
     * Obtener subforos de un foro padre
     */
    public function get_subforums($parent_id) {
        global $wpdb;
        $table = $this->db->get_table('forums');
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} 
             WHERE parent_id = %d 
             AND is_active = 1 
             ORDER BY display_order ASC",
            $parent_id
        ));
    }
    
    /**
     * Obtener foro por slug
     */
    public function get_by_slug($slug) {
        return $this->db->get_row('forums', ['slug' => $slug]);
    }
    
    /**
     * Actualizar contador de topics
     */
    public function update_topic_count($forum_id) {
        global $wpdb;
        $forums_table = $this->db->get_table('forums');
        $topics_table = $this->db->get_table('topics');
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$topics_table} 
             WHERE forum_id = %d 
             AND status = 'approved'",
            $forum_id
        ));
        
        $this->db->update('forums', 
            ['topic_count' => $count],
            ['id' => $forum_id]
        );
    }
}
