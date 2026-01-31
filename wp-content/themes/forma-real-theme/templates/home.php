<?php
/**
 * Template: Home Page
 */
get_header();

$topic_handler  = new FR_Topic();
$recent_topics  = $topic_handler->get_recent_topics(6);

// Quick stats — pull from DB
global $wpdb;
$total_topics  = (int) $wpdb->get_var("SELECT SUM(topic_count) FROM {$wpdb->prefix}fr_forums WHERE is_active = 1");
$total_replies = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}fr_replies WHERE status = 'approved'");
$total_members = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}users");
?>

<!-- ═══════════════════════════════════════ HERO ═══════════════════════════════════════ -->
<section class="hero">
    <div class="container">
        <div class="hero-inner">

            <!-- Live badge -->
            <div class="hero-badge">
                <span class="live-dot"></span>
                Comunidad activa · <?php echo number_format($total_members); ?> miembros
            </div>

            <h1>Fitness <span class="grad">Real</span>,<br>Resultados Reales.</h1>

            <p>Una comunidad donde la experiencia supera a la teoría. Comparte tus rutinas, resuelve dudas y documenta tu progreso sin filtros.</p>

            <div class="hero-ctas">
                <a href="<?php echo esc_url(home_url('/foro/')); ?>" class="btn btn-primary btn-lg">Explorar el Foro</a>
                <?php if (!is_user_logged_in()) : ?>
                    <a href="<?php echo esc_url(wp_registration_url()); ?>" class="btn btn-ghost btn-lg">Unirse gratis</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════ STATS BAR ═══════════════════════════════════════ -->
<div class="stats-bar">
    <div class="container">
        <div class="stat-item">
            <div class="stat-icon">💬</div>
            <div>
                <div class="stat-value"><?php echo number_format($total_topics); ?></div>
                <div class="stat-label">Temas</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">📝</div>
            <div>
                <div class="stat-value"><?php echo number_format($total_replies); ?></div>
                <div class="stat-label">Respuestas</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">👥</div>
            <div>
                <div class="stat-value"><?php echo number_format($total_members); ?></div>
                <div class="stat-label">Miembros</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">📂</div>
            <div>
                <div class="stat-value">4</div>
                <div class="stat-label">Categorías</div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════ ACTIVITY ═══════════════════════════════════════ -->
<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <h2>Actividad Reciente</h2>
                <p class="sub">Lo que está pasando en la comunidad</p>
            </div>
            <a href="<?php echo esc_url(home_url('/foro/')); ?>" class="section-link">Ver todo →</a>
        </div>

        <div class="space-y-3">
            <?php if ($recent_topics) : ?>
                <?php foreach ($recent_topics as $topic) : ?>
                <div class="activity-card anim-up">

                    <div class="ac-avatar">
                        <?php echo get_avatar($topic->user_id, 42, '', '', ['echo' => false]); ?>
                    </div>

                    <div class="ac-body">
                        <div class="ac-title">
                            <a href="<?php echo esc_url(home_url('/foro/' . $topic->forum_slug . '/' . $topic->slug)); ?>">
                                <?php echo esc_html($topic->title); ?>
                            </a>
                        </div>
                        <div class="ac-meta">
                            <span>por <strong><?php echo esc_html($topic->author_name); ?></strong></span>
                            <span class="dot"></span>
                            <span>en <a href="<?php echo esc_url(home_url('/foro/' . $topic->forum_slug)); ?>"><?php echo esc_html($topic->forum_name); ?></a></span>
                            <span class="dot"></span>
                            <span><?php echo FR_Helpers::time_ago($topic->created_at); ?></span>
                        </div>
                    </div>

                    <div class="ac-replies">
                        <div class="num"><?php echo number_format($topic->reply_count); ?></div>
                        <div class="lbl">resp.</div>
                    </div>

                </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state">
                    <span class="empty-icon">🏋️</span>
                    <h3>Aún no hay actividad</h3>
                    <p>¡Sé el primero en publicar en el foro!</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php get_footer(); ?>
