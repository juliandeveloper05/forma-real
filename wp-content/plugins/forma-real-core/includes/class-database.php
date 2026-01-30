<?php
/**
 * Database Handler
 * Maneja todas las queries a la base de datos custom de Forma Real
 */
class FR_Database {
    private static $instance = null;
    private $wpdb;
    private $tables = [];
    
    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        // Definir nombres de tablas con el prefijo de WordPress y el prefijo del plugin 'fr_'
        $this->tables = [
            'forums'        => $wpdb->prefix . 'fr_forums',
            'topics'        => $wpdb->prefix . 'fr_topics',
            'replies'       => $wpdb->prefix . 'fr_replies',
            'profiles'      => $wpdb->prefix . 'fr_user_profiles',
            'reports'       => $wpdb->prefix . 'fr_reports',
            'notifications' => $wpdb->prefix . 'fr_notifications',
            'topic_views'   => $wpdb->prefix . 'fr_topic_views',
            'user_activity' => $wpdb->prefix . 'fr_user_activity',
        ];
    }
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function get_table($name) {
        return isset($this->tables[$name]) ? $this->tables[$name] : null;
    }
    
    // Métodos CRUD básicos
    public function insert($table, $data) {
        return $this->wpdb->insert(
            $this->get_table($table),
            $data,
            $this->get_format($data)
        );
    }
    
    public function update($table, $data, $where) {
        return $this->wpdb->update(
            $this->get_table($table),
            $data,
            $where,
            $this->get_format($data),
            $this->get_format($where)
        );
    }
    
    public function delete($table, $where) {
        return $this->wpdb->delete(
            $this->get_table($table),
            $where,
            $this->get_format($where)
        );
    }
    
    public function get_row($table, $where) {
        $table_name = $this->get_table($table);
        $where_clause = $this->build_where_clause($where);
        
        return $this->wpdb->get_row(
            "SELECT * FROM {$table_name} WHERE {$where_clause}"
        );
    }

    // Exponer objeto wpdb para queries personalizadas si hace falta
    public function get_wpdb() {
        return $this->wpdb;
    }
    
    private function get_format($data) {
        $formats = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $formats[] = '%d';
            } elseif (is_float($value)) {
                $formats[] = '%f';
            } else {
                $formats[] = '%s';
            }
        }
        return $formats;
    }
    
    private function build_where_clause($where) {
        $clauses = [];
        foreach ($where as $column => $value) {
            $clauses[] = $this->wpdb->prepare("{$column} = %s", $value);
        }
        return implode(' AND ', $clauses);
    }
}
