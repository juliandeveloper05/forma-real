<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container flex justify-between items-center w-full h-full">
        
        <!-- Logo -->
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <h1 class="site-title text-xl font-bold">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-secondary hover:text-primary">
                        Forma Real
                    </a>
                </h1>
            <?php endif; ?>
        </div>

        <!-- Navigation -->
        <nav class="site-navigation hidden md:block">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => false,
                'menu_class'     => 'flex space-x-6 font-medium text-sm',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>

        <!-- User Actions -->
        <div class="user-actions flex items-center gap-4">
            <?php if (is_user_logged_in()) : ?>
                <?php $current_user = wp_get_current_user(); ?>
                <a href="<?php echo home_url('/perfil'); ?>" class="flex items-center gap-2">
                    <span class="text-sm font-medium hide-mobile"><?php echo esc_html($current_user->display_name); ?></span>
                    <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200">
                        <?php echo get_avatar($current_user->ID, 32); ?>
                    </div>
                </a>
            <?php else : ?>
                <a href="<?php echo wp_login_url(); ?>" class="text-sm font-medium hover:text-primary">Entrar</a>
                <a href="<?php echo wp_registration_url(); ?>" class="btn btn-primary btn-sm">Registro</a>
            <?php endif; ?>
        </div>
        
    </div>
</header>

<main id="primary" class="site-main">
