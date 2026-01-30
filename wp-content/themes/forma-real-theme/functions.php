<?php
/**
 * Forma Real Theme Functions
 */

if (!defined('FR_THEME_VERSION')) {
    define('FR_THEME_VERSION', '1.0.0');
}

/**
 * Theme Setup
 */
function fr_theme_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Title Tag Support
    add_theme_support('title-tag');

    // Feautured Images
    add_theme_support('post-thumbnails');

    // Register Menus
    register_nav_menus([
        'primary' => esc_html__('Primary Menu', 'forma-real-theme'),
        'footer'  => esc_html__('Footer Menu', 'forma-real-theme'),
    ]);

    // HTML5 Support
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);
}
add_action('after_setup_theme', 'fr_theme_setup');

/**
 * Enqueue scripts and styles
 */
function fr_enqueue_scripts() {
    // Main Style (style.css root)
    wp_enqueue_style('forma-real-style', get_stylesheet_uri(), [], FR_THEME_VERSION);

    // Google Fonts (Inter)
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', [], null);

    // Main JS
    // wp_enqueue_script('forma-real-js', get_template_directory_uri() . '/assets/js/main.js', [], FR_THEME_VERSION, true);

    // AJAX Handling
    wp_localize_script('forma-real-js', 'fr_ajax_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('fr_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'fr_enqueue_scripts');

/**
 * Registrar Sidebars
 */
function fr_widgets_init() {
    register_sidebar([
        'name'          => esc_html__('Sidebar Principal', 'forma-real-theme'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Añade widgets aquí.', 'forma-real-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s card mb-4">',
        'after_widget'  => '</section>',
        'before_title'  => '<div class="card-header"><h3 class="widget-title text-lg font-semibold">',
        'after_title'   => '</h3></div>',
    ]);
}
add_action('widgets_init', 'fr_widgets_init');

/**
 * Routing & Permalinks
 */
// 1. Registrar reglas de reescritura
function fr_custom_rewrite_rules() {
    // Foro principal (/foro)
    add_rewrite_rule(
        '^foro/?$',
        'index.php?fr_page=forum_index',
        'top'
    );
    
    // Categoría individual (/foro/rutinas)
    add_rewrite_rule(
        '^foro/([^/]+)/?$',
        'index.php?fr_page=forum_category&forum_slug=$matches[1]',
        'top'
    );
    
    // Tema individual (/foro/rutinas/mi-tema)
    add_rewrite_rule(
        '^foro/([^/]+)/([^/]+)/?$',
        'index.php?fr_page=forum_topic&forum_slug=$matches[1]&topic_slug=$matches[2]',
        'top'
    );

    // Perfil usuario (/perfil/usuario)
    add_rewrite_rule(
        '^perfil/([^/]+)/?$',
        'index.php?fr_page=user_profile&username=$matches[1]',
        'top'
    );
}
add_action('init', 'fr_custom_rewrite_rules');

// 2. Registrar variables de consulta (query vars)
function fr_query_vars($vars) {
    $vars[] = 'fr_page';
    $vars[] = 'forum_slug';
    $vars[] = 'topic_slug';
    $vars[] = 'username';
    return $vars;
}
add_filter('query_vars', 'fr_query_vars');

// 3. Redirigir a las plantillas correctas
function fr_template_redirect() {
    $fr_page = get_query_var('fr_page');
    
    if (!$fr_page) {
        return;
    }
    
    $template_path = get_template_directory() . '/templates/';
    
    switch ($fr_page) {
        case 'forum_index':
            include $template_path . 'forum-index.php';
            exit;
        case 'forum_category':
            include $template_path . 'forum-category.php';
            exit;
        case 'forum_topic':
            include $template_path . 'topic-single.php';
            exit;
        case 'user_profile':
            include $template_path . 'profile.php';
            exit;
    }
}
add_action('template_redirect', 'fr_template_redirect');
