<?php
/**
 * Forma Real UI Seeder
 * 
 * Handles cache clearing, option updates, and UI initialization
 * Run via WP-CLI: wp eval "FR_UI_Seeder::run();"
 * Or call directly from functions.php for first-time setup
 * 
 * @package FormaReal
 * @since 2.0.0
 * @author Julian
 */

if (!defined('ABSPATH')) {
    exit;
}

class FR_UI_Seeder {
    
    /**
     * UI Version
     */
    const VERSION = '2.0.0';
    
    /**
     * Theme option key
     */
    const OPTION_KEY = 'forma_real_ui_version';
    
    /**
     * Run the seeder
     */
    public static function run() {
        $instance = new self();
        return $instance->seed();
    }
    
    /**
     * Main seeding process
     */
    public function seed() {
        $results = array(
            'success' => true,
            'messages' => array(),
            'version' => self::VERSION,
            'timestamp' => current_time('mysql'),
        );
        
        // Step 1: Clear all transients
        $this->clear_transients();
        $results['messages'][] = '✓ Transients cleared';
        
        // Step 2: Clear object cache
        $this->clear_object_cache();
        $results['messages'][] = '✓ Object cache cleared';
        
        // Step 3: Flush rewrite rules
        $this->flush_rewrite_rules();
        $results['messages'][] = '✓ Rewrite rules flushed';
        
        // Step 4: Clear popular cache plugins
        $this->clear_plugin_caches();
        $results['messages'][] = '✓ Plugin caches cleared';
        
        // Step 5: Update version option
        update_option(self::OPTION_KEY, array(
            'version' => self::VERSION,
            'updated_at' => current_time('mysql'),
            'updated_by' => get_current_user_id() ?: 'system',
        ));
        $results['messages'][] = '✓ Version updated to ' . self::VERSION;
        
        // Step 6: Set default theme mods if not exist
        $this->set_default_theme_mods();
        $results['messages'][] = '✓ Theme mods initialized';
        
        // Step 7: Register custom image sizes
        $this->register_image_sizes();
        $results['messages'][] = '✓ Image sizes registered';
        
        // Log the update
        $this->log_update($results);
        
        return $results;
    }
    
    /**
     * Clear all transients
     */
    private function clear_transients() {
        global $wpdb;
        
        // Delete expired transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_timeout_%' 
             AND option_value < UNIX_TIMESTAMP()"
        );
        
        // Delete forma-real specific transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_fr_%' 
             OR option_name LIKE '_transient_forma_real_%'"
        );
        
        // Delete site transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_site_transient_fr_%'"
        );
    }
    
    /**
     * Clear object cache
     */
    private function clear_object_cache() {
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
    
    /**
     * Flush rewrite rules
     */
    private function flush_rewrite_rules() {
        flush_rewrite_rules(false);
    }
    
    /**
     * Clear popular cache plugins
     */
    private function clear_plugin_caches() {
        // WP Super Cache
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        // W3 Total Cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        
        // WP Rocket
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        
        // LiteSpeed Cache
        if (class_exists('LiteSpeed_Cache_API')) {
            LiteSpeed_Cache_API::purge_all();
        }
        
        // Autoptimize
        if (class_exists('autoptimizeCache')) {
            autoptimizeCache::clearall();
        }
        
        // WP Fastest Cache
        if (function_exists('wpfc_clear_all_cache')) {
            wpfc_clear_all_cache(true);
        }
        
        // Cache Enabler
        if (class_exists('Cache_Enabler')) {
            Cache_Enabler::clear_total_cache();
        }
    }
    
    /**
     * Set default theme mods
     */
    private function set_default_theme_mods() {
        $defaults = array(
            'fr_primary_color' => '#2563eb',
            'fr_success_color' => '#10b981',
            'fr_warning_color' => '#f59e0b',
            'fr_danger_color' => '#ef4444',
            'fr_enable_animations' => true,
            'fr_footer_social_links' => true,
            'fr_notification_sound' => true,
        );
        
        foreach ($defaults as $key => $value) {
            if (get_theme_mod($key) === false) {
                set_theme_mod($key, $value);
            }
        }
    }
    
    /**
     * Register custom image sizes
     */
    private function register_image_sizes() {
        // Card thumbnails
        add_image_size('fr-card-thumb', 400, 300, true);
        
        // Profile avatars
        add_image_size('fr-avatar-lg', 200, 200, true);
        add_image_size('fr-avatar-md', 80, 80, true);
        add_image_size('fr-avatar-sm', 40, 40, true);
        
        // Hero images
        add_image_size('fr-hero', 1920, 600, true);
    }
    
    /**
     * Log the update
     */
    private function log_update($results) {
        $log_file = get_template_directory() . '/logs/ui-seeder.log';
        $log_dir = dirname($log_file);
        
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }
        
        $log_entry = sprintf(
            "[%s] UI Seeder v%s executed - %s\n",
            current_time('Y-m-d H:i:s'),
            self::VERSION,
            $results['success'] ? 'SUCCESS' : 'FAILED'
        );
        
        @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Check if update is needed
     */
    public static function needs_update() {
        $current = get_option(self::OPTION_KEY, array());
        $current_version = isset($current['version']) ? $current['version'] : '0.0.0';
        
        return version_compare($current_version, self::VERSION, '<');
    }
    
    /**
     * Auto-run on theme switch or when version mismatch
     */
    public static function maybe_auto_seed() {
        if (self::needs_update() && current_user_can('manage_options')) {
            self::run();
        }
    }
}

// Hook for automatic seeding on admin init
add_action('admin_init', array('FR_UI_Seeder', 'maybe_auto_seed'));

// Hook for theme activation
add_action('after_switch_theme', array('FR_UI_Seeder', 'run'));
