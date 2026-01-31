<?php
/**
 * Forma Real Theme Functions
 */

if (!defined('FR_THEME_VERSION')) {
    define('FR_THEME_VERSION', '2.0.0');
}

/**
 * Theme Setup
 */
function fr_theme_setup() {
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);

    register_nav_menus([
        'primary' => esc_html__('Primary Menu', 'forma-real-theme'),
        'footer'  => esc_html__('Footer Menu', 'forma-real-theme'),
    ]);
}
add_action('after_setup_theme', 'fr_theme_setup');

/**
 * Enqueue scripts and styles
 */
function fr_enqueue_scripts() {
    // Core stylesheet
    wp_enqueue_style('forma-real-style', get_stylesheet_uri(), [], FR_THEME_VERSION);
    // Responsive utilities
    wp_enqueue_style('forma-real-responsive', get_template_directory_uri() . '/assets/css/responsive.css', ['forma-real-style'], FR_THEME_VERSION);
    // Google Fonts — Barlow Condensed (display) + Outfit (body)
    wp_enqueue_style('fr-fonts',
        'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap',
        [], null
    );
    // Main JS
    wp_enqueue_script('forma-real-js', get_template_directory_uri() . '/assets/js/main.js', [], FR_THEME_VERSION, true);
    // AJAX config
    wp_localize_script('forma-real-js', 'fr_ajax_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('fr_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'fr_enqueue_scripts');

/**
 * Sidebars
 */
function fr_widgets_init() {
    register_sidebar([
        'name'          => esc_html__('Sidebar', 'forma-real-theme'),
        'id'            => 'sidebar-1',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'fr_widgets_init');

/**
 * Routing & Permalinks
 */
function fr_custom_rewrite_rules() {
    add_rewrite_rule('^foro/?$',                            'index.php?fr_page=forum_index', 'top');
    add_rewrite_rule('^foro/([^/]+)/?$',                    'index.php?fr_page=forum_category&forum_slug=$matches[1]', 'top');
    add_rewrite_rule('^foro/([^/]+)/([^/]+)/?$',            'index.php?fr_page=forum_topic&forum_slug=$matches[1]&topic_slug=$matches[2]', 'top');
    add_rewrite_rule('^perfil/([^/]+)/?$',                  'index.php?fr_page=user_profile&username=$matches[1]', 'top');
}
add_action('init', 'fr_custom_rewrite_rules');

function fr_query_vars($vars) {
    $vars[] = 'fr_page';
    $vars[] = 'forum_slug';
    $vars[] = 'topic_slug';
    $vars[] = 'username';
    return $vars;
}
add_filter('query_vars', 'fr_query_vars');

function fr_template_redirect() {
    $fr_page = get_query_var('fr_page');
    if (!$fr_page) return;

    $dir = get_template_directory() . '/templates/';
    $map = [
        'forum_index'    => 'forum-index.php',
        'forum_category' => 'forum-category.php',
        'forum_topic'    => 'topic-single.php',
        'user_profile'   => 'profile.php',
    ];

    if (isset($map[$fr_page])) {
        include $dir . $map[$fr_page];
        exit;
    }
}
add_action('template_redirect', 'fr_template_redirect');
