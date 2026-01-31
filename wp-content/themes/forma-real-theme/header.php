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
                <?php $u = wp_get_current_user(); ?>
                <a href="<?php echo esc_url(home_url('/perfil/' . $u->user_login)); ?>" class="user-pill">
                    <?php echo get_avatar($u->ID, 30, '', '', ['echo' => false, 'class' => '']); ?>
                    <span class="user-name"><?php echo esc_html($u->display_name); ?></span>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(wp_login_url()); ?>" class="btn-login">Entrar</a>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-primary btn-sm">Registro</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<main id="primary" class="site-main">
