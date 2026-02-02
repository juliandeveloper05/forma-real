<?php
/**
 * Plugin Name: Forma Real Core
 * Plugin URI: https://github.com/tu-usuario/forma-real
 * Description: Core functionality for Forma Real Portfolio Project. Implements Custom Database Tables, Forum Logic, and OOP Classes.
 * Version: 1.0.0
 * Author: Tu Nombre
 * License: MIT
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('FR_CORE_PATH', plugin_dir_path(__FILE__));
define('FR_CORE_URL', plugin_dir_url(__FILE__));
define('FR_CORE_VERSION', '1.0.0');

// Autoload de clases (manual por ahora, luego podríamos usar Composer)
require_once FR_CORE_PATH . 'includes/class-database.php';
require_once FR_CORE_PATH . 'includes/class-forum.php';
require_once FR_CORE_PATH . 'includes/class-topic.php';
require_once FR_CORE_PATH . 'includes/class-reply.php';
require_once FR_CORE_PATH . 'includes/class-user-profile.php';
require_once FR_CORE_PATH . 'includes/class-helpers.php';
require_once FR_CORE_PATH . 'includes/class-ajax-handler.php';
require_once FR_CORE_PATH . 'includes/class-search.php';
require_once FR_CORE_PATH . 'includes/class-moderation.php';
require_once FR_CORE_PATH . 'includes/class-notification.php';

/**
 * Clase principal del Plugin (Singleton)
 */
class Forma_Real_Core {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Hooks de activación y desactivación
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Inicializar componentes cuando WordPress carga
        add_action('plugins_loaded', [$this, 'init_components']);
        
        // Verificar si hay tablas pendientes de crear (para installs existentes)
        add_action('admin_init', [$this, 'maybe_update_tables']);
    }

    
    public function init_components() {
        // Instanciar AJAX handler para escuchar peticiones
        new FR_Ajax_Handler();
    }
    
    /**
     * Tareas de activación: Crear tablas
     */
    public function activate() {
        $this->create_tables();
        flush_rewrite_rules();
    }
    
    /**
     * Crear o actualizar todas las tablas del plugin
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Tabla Forums
        $sql_forums = "CREATE TABLE {$wpdb->prefix}fr_forums (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL UNIQUE,
            description TEXT,
            icon VARCHAR(50),
            color VARCHAR(7) DEFAULT '#3b82f6',
            parent_id BIGINT UNSIGNED NULL,
            display_order INT DEFAULT 0,
            topic_count INT DEFAULT 0,
            reply_count INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_parent (parent_id),
            INDEX idx_active (is_active)
        ) $charset_collate;";
        
        // Tabla Topics
        $sql_topics = "CREATE TABLE {$wpdb->prefix}fr_topics (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            forum_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content LONGTEXT NOT NULL,
            status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'approved',
            is_sticky TINYINT(1) DEFAULT 0,
            is_closed TINYINT(1) DEFAULT 0,
            view_count INT DEFAULT 0,
            reply_count INT DEFAULT 0,
            last_reply_id BIGINT UNSIGNED NULL,
            last_active_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_forum (forum_id),
            INDEX idx_user (user_id),
            INDEX idx_slug (slug),
            FULLTEXT INDEX idx_fulltext (title, content)
        ) $charset_collate;";
        
        // Tabla Replies
        $sql_replies = "CREATE TABLE {$wpdb->prefix}fr_replies (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            topic_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            parent_id BIGINT UNSIGNED NULL,
            content LONGTEXT NOT NULL,
            status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'approved',
            is_solution TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_topic (topic_id),
            INDEX idx_user (user_id),
            FULLTEXT INDEX idx_content (content)
        ) $charset_collate;";
        
        // Tabla Notifications
        $sql_notifications = "CREATE TABLE {$wpdb->prefix}fr_notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            type VARCHAR(50) NOT NULL,
            content TEXT NOT NULL,
            link VARCHAR(500),
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user (user_id),
            INDEX idx_unread (user_id, is_read),
            INDEX idx_created (created_at)
        ) $charset_collate;";
        
        // Tabla User Profiles
        $sql_profiles = "CREATE TABLE {$wpdb->prefix}fr_user_profiles (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL UNIQUE,
            bio TEXT,
            location VARCHAR(100),
            website VARCHAR(255),
            social_links TEXT,
            avatar_url VARCHAR(500),
            cover_url VARCHAR(500),
            topic_count INT DEFAULT 0,
            reply_count INT DEFAULT 0,
            reputation INT DEFAULT 0,
            last_active DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user (user_id),
            INDEX idx_reputation (reputation)
        ) $charset_collate;";
        
        // Tabla Reports (moderación)
        $sql_reports = "CREATE TABLE {$wpdb->prefix}fr_reports (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            reporter_id BIGINT UNSIGNED NOT NULL,
            reported_type ENUM('topic', 'reply', 'user') NOT NULL,
            reported_id BIGINT UNSIGNED NOT NULL,
            reason VARCHAR(100) NOT NULL,
            details TEXT,
            status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
            moderator_id BIGINT UNSIGNED NULL,
            moderator_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            resolved_at DATETIME NULL,
            INDEX idx_status (status),
            INDEX idx_reporter (reporter_id),
            INDEX idx_reported (reported_type, reported_id)
        ) $charset_collate;";
        
        // Ejecutar dbDelta para crear/actualizar tablas
        dbDelta($sql_forums);
        dbDelta($sql_topics);
        dbDelta($sql_replies);
        dbDelta($sql_notifications);
        dbDelta($sql_profiles);
        dbDelta($sql_reports);
        
        // Marcar versión de DB para futuras migraciones
        update_option('fr_core_db_version', '1.1.0');
    }
    
    /**
     * Verificar y actualizar tablas en admin_init (para installs existentes)
     */
    public function maybe_update_tables() {
        $current_version = get_option('fr_core_db_version', '1.0.0');
        if (version_compare($current_version, '1.1.0', '<')) {
            $this->create_tables();
        }
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Iniciar el plugin
Forma_Real_Core::get_instance();
