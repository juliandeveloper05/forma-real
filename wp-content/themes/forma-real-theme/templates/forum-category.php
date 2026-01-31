<?php
/**
 * Template: Forum Category
 */
get_header();

$forum_slug    = get_query_var('forum_slug');
$forum_handler = new FR_Forum();
$forum         = $forum_handler->get_by_slug($forum_slug);

if (!$forum) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit();
}

$topic_handler = new FR_Topic();
$page          = get_query_var('paged') ?: 1;
$topics        = $topic_handler->get_by_forum($forum->id, $page);
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="<?php echo esc_url(home_url('/foro/')); ?>">Foro</a>
        <span class="sep">›</span>
        <span class="current"><?php echo esc_html($forum->name); ?></span>
    </nav>

    <!-- Category Header -->
    <div class="cat-header">
        <div class="cat-header-left">
            <div class="cat-icon" style="background-color: <?php echo esc_attr($forum->color); ?>18; color: <?php echo esc_attr($forum->color); ?>;">
                <?php echo !empty($forum->icon) ? esc_html($forum->icon) : '📂'; ?>
            </div>
            <div>
                <h1><?php echo esc_html($forum->name); ?></h1>
                <p class="cat-desc"><?php echo esc_html($forum->description); ?></p>
            </div>
        </div>

        <?php if (is_user_logged_in()) : ?>
            <button onclick="document.getElementById('new-topic-modal').showDialog()" class="btn btn-primary">+ Nuevo Tema</button>
        <?php else : ?>
            <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="btn btn-outline btn-sm">Inicia sesión para postear</a>
        <?php endif; ?>
    </div>

    <!-- Topic List -->
    <div class="space-y-2">
        <?php if ($topics) : ?>
            <?php foreach ($topics as $topic) : ?>
            <div class="topic-card anim-up
                <?php echo $topic->is_sticky ? 'sticky' : ''; ?>
                <?php echo $topic->is_closed ? 'closed' : ''; ?>">

                <!-- Status icon -->
                <div class="tc-status">
                    <?php
                    if ($topic->is_sticky)      echo '📌';
                    elseif ($topic->is_closed)  echo '🔒';
                    else                        echo '💬';
                    ?>
                </div>

                <!-- Main -->
                <div class="tc-main">
                    <div class="tc-title-row">
                        <div class="tc-title">
                            <a href="<?php echo esc_url(home_url('/foro/' . $forum->slug . '/' . $topic->slug)); ?>">
                                <?php echo esc_html($topic->title); ?>
                            </a>
                        </div>
                        <?php if ($topic->is_sticky) : ?>
                            <span class="tc-sticky-tag">📌 Fijado</span>
                        <?php endif; ?>
                    </div>
                    <div class="tc-meta">
                        <span>por <strong><?php echo esc_html($topic->author_name); ?></strong></span>
                        <span class="dot"></span>
                        <span><?php echo FR_Helpers::time_ago($topic->created_at); ?></span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="tc-stats">
                    <div class="tc-stat">
                        <div class="s-num"><?php echo number_format($topic->reply_count); ?></div>
                        <div class="s-lbl">Resp.</div>
                    </div>
                    <div class="tc-stat">
                        <div class="s-num"><?php echo number_format($topic->view_count); ?></div>
                        <div class="s-lbl">Vistas</div>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="empty-state">
                <span class="empty-icon">📭</span>
                <h3>No hay temas en este foro</h3>
                <p>¡Sé el primero en iniciar una conversación!</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- ═══ Modal: Nuevo Tema ═══ -->
<?php if (is_user_logged_in()) : ?>
<dialog id="new-topic-modal">
    <div class="modal-head">
        <h2>Crear Nuevo Tema</h2>
        <button class="modal-close" onclick="document.getElementById('new-topic-modal').close()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="create-topic-form">
            <input type="hidden" name="action"   value="fr_create_topic">
            <input type="hidden" name="forum_id" value="<?php echo esc_attr($forum->id); ?>">

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Título</label>
                <input type="text" name="title" required class="form-input" placeholder="¿Sobre qué quieres hablar?">
            </div>
            <div style="margin-bottom: 0.5rem;">
                <label class="form-label">Contenido</label>
                <textarea name="content" rows="5" required class="form-textarea" placeholder="Describe tu tema en detalle…"></textarea>
            </div>

            <div class="form-actions">
                <button type="button" onclick="document.getElementById('new-topic-modal').close()" class="btn btn-outline btn-sm">Cancelar</button>
                <button type="submit" class="btn btn-primary">Publicar Tema</button>
            </div>
        </form>
    </div>
</dialog>
<?php endif; ?>

<?php get_footer(); ?>
