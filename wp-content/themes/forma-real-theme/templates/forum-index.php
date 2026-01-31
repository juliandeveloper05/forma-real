<?php
/**
 * Template: Forum Index
 */
get_header();

$forum_handler = new FR_Forum();
$forums = $forum_handler->get_all_forums();
?>

<div class="container" style="padding-top: 2.5rem; padding-bottom: 4rem;">

    <!-- Page Header -->
    <div class="section-head" style="margin-bottom: 1.75rem;">
        <div>
            <h1 style="font-size: clamp(1.6rem, 3.5vw, 2.1rem);">Foros de Discusión</h1>
            <p class="sub">Únete a la conversación sobre entrenamiento real y nutrición</p>
        </div>
        <?php if (is_user_logged_in()) : ?>
            <!-- Optional: quick-create link could go here -->
        <?php endif; ?>
    </div>

    <!-- Forum List -->
    <div class="space-y-3">
        <?php if ($forums) : ?>
            <?php foreach ($forums as $forum) : ?>
            <a href="<?php echo esc_url(home_url('/foro/' . $forum->slug)); ?>" class="forum-card anim-up">

                <!-- Icon -->
                <div class="forum-icon" style="background-color: <?php echo esc_attr($forum->color); ?>15; color: <?php echo esc_attr($forum->color); ?>;">
                    <?php echo !empty($forum->icon) ? esc_html($forum->icon) : '💬'; ?>
                </div>

                <!-- Info -->
                <div class="forum-info">
                    <div class="forum-name"><?php echo esc_html($forum->name); ?></div>
                    <p class="forum-desc"><?php echo esc_html($forum->description); ?></p>
                    <div class="forum-stats-row">
                        <span class="forum-stat"><span>📄</span> <?php echo number_format($forum->topic_count); ?> temas</span>
                        <span class="forum-stat"><span>💬</span> <?php echo number_format($forum->reply_count); ?> respuestas</span>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="forum-arrow">→</div>

            </a>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="empty-state">
                <span class="empty-icon">📭</span>
                <h3>No hay foros aún</h3>
                <p>
                    <?php if (current_user_can('manage_options')) : ?>
                        Ve a la base de datos y crea las categorías del foro.
                    <?php else : ?>
                        Vuelve pronto, estamos preparando el contenido.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php get_footer(); ?>
