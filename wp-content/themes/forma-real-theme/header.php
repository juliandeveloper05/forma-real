<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header">
    <div class="container">

        <!-- Logo -->
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <h1 class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">Forma Real</a>
                </h1>
            <?php endif; ?>
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="site-search hidden md:block">
            <form action="<?php echo home_url('/buscar/'); ?>" method="GET" class="flex">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Buscar en el foro..."
                    class="px-3 py-2 text-sm rounded-l-lg border border-gray-300 focus:border-primary focus:outline-none w-48 lg:w-64"
                >
                <button type="submit" class="px-3 py-2 bg-primary text-white rounded-r-lg hover:bg-primary-dark transition-colors">
                    🔍
                </button>
            </form>
        </div>

        <!-- Primary Nav -->
        <nav class="site-navigation" aria-label="Navegación principal">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => '',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>

        <!-- User Actions -->
        <div class="user-actions">
            <?php if (is_user_logged_in()) : ?>
                <!-- Notifications -->
                <?php get_template_part('partials/notifications-dropdown'); ?>

                <?php $u = wp_get_current_user(); ?>
                <a href="<?php echo esc_url(home_url('/perfil/' . $u->user_login)); ?>" class="user-pill">
                    <?php echo get_avatar($u->ID, 30, '', '', ['echo' => false, 'class' => '']); ?>
                    <span class="user-name"><?php echo esc_html($u->display_name); ?></span>
                </a>

                <?php if (current_user_can('moderate_comments')) : ?>
                    <a href="<?php echo home_url('/moderacion/'); ?>" class="ml-2 text-gray-600 hover:text-primary" title="Panel de Moderación">
                        🛡️
                    </a>
                <?php endif; ?>
            <?php else : ?>
                <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn-login">Entrar</a>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary btn-sm">Registro</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<!-- Mobile Search (visible on mobile) -->
<div class="md:hidden bg-gray-100 px-4 py-2">
    <form action="<?php echo home_url('/buscar/'); ?>" method="GET" class="flex">
        <input 
            type="text" 
            name="q" 
            placeholder="Buscar..."
            class="flex-grow px-3 py-2 text-sm rounded-l-lg border border-gray-300 focus:border-primary focus:outline-none"
        >
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-r-lg">
            🔍
        </button>
    </form>
</div>

<main id="primary" class="site-main">
