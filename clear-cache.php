<?php
/**
 * Forma Real - Quick Cache Clear Script
 * 
 * Run directly from browser: http://forma-real.test/clear-cache.php
 * For development/staging environments only!
 * 
 * @package FormaReal
 * @since 2.0.0
 */

// Security: Only allow in development
$allowed_hosts = array('localhost', '127.0.0.1', 'forma-real.test');
if (!in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    die('⛔ Access denied. This script is only available in development environments.');
}

// Optional: Require secret key
$secret_key = 'fr_clear_2024'; // Change this!
if (isset($_GET['key']) && $_GET['key'] !== $secret_key) {
    die('⛔ Invalid key.');
}

// Load WordPress
require_once dirname(__FILE__) . '/wp-load.php';

// Check user capability (if logged in)
if (is_user_logged_in() && !current_user_can('manage_options')) {
    die('⛔ You do not have permission to clear the cache.');
}

// Clear caches
global $wpdb;

$results = array();

// 1. Clear transients
$deleted = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
$results[] = "✓ Cleared {$deleted} transients";

// 2. Clear object cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    $results[] = "✓ Object cache flushed";
}

// 3. Flush rewrite rules
flush_rewrite_rules(false);
$results[] = "✓ Rewrite rules flushed";

// 4. Clear plugin caches
$plugins_cleared = array();

if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
    $plugins_cleared[] = 'WP Super Cache';
}

if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
    $plugins_cleared[] = 'W3 Total Cache';
}

if (function_exists('rocket_clean_domain')) {
    rocket_clean_domain();
    $plugins_cleared[] = 'WP Rocket';
}

if (!empty($plugins_cleared)) {
    $results[] = "✓ Plugin caches cleared: " . implode(', ', $plugins_cleared);
}

// 5. Update UI version
update_option('forma_real_cache_cleared', array(
    'timestamp' => current_time('mysql'),
    'by' => is_user_logged_in() ? wp_get_current_user()->user_login : 'anonymous',
));
$results[] = "✓ Cache clear logged";

// Output results
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cache Cleared - Forma Real</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        h1 {
            font-size: 24px;
            color: #10b981;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        h1::before { content: '✅'; font-size: 32px; }
        .subtitle {
            color: #64748b;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .results {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .results li {
            list-style: none;
            padding: 8px 0;
            color: #334155;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .results li:last-child { border-bottom: none; }
        .timestamp {
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            margin-bottom: 24px;
        }
        .actions {
            display: flex;
            gap: 12px;
        }
        .btn {
            flex: 1;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Cache Cleared!</h1>
        <p class="subtitle">All WordPress caches have been successfully cleared.</p>
        
        <ul class="results">
            <?php foreach ($results as $result): ?>
                <li><?php echo esc_html($result); ?></li>
            <?php endforeach; ?>
        </ul>
        
        <p class="timestamp">
            Executed at: <?php echo current_time('F j, Y g:i:s A'); ?>
        </p>
        
        <div class="actions">
            <a href="<?php echo home_url(); ?>" class="btn btn-primary">Go to Site</a>
            <a href="<?php echo admin_url(); ?>" class="btn btn-secondary">Admin Panel</a>
        </div>
    </div>
</body>
</html>
<?php
exit;
