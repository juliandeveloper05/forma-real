<?php
/**
 * Template: Topic Single
 */
get_header();

$forum_slug    = get_query_var('forum_slug');
$topic_slug    = get_query_var('topic_slug');
$forum_handler = new FR_Forum();
$topic_handler = new FR_Topic();
$reply_handler = new FR_Reply();

$forum = $forum_handler->get_by_slug($forum_slug);

global $wpdb;
$topic_id = $wpdb->get_var($wpdb->prepare(
    "SELECT id FROM {$wpdb->prefix}fr_topics WHERE slug = %s", $topic_slug
));

if (!$topic_id) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit();
}

$topic  = $topic_handler->get_topic_full($topic_id);
$replies = $reply_handler->get_by_topic($topic_id);
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">

    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="<?php echo esc_url(home_url('/foro/')); ?>">Foro</a>
        <span class="sep">›</span>
        <?php if ($forum) : ?>
            <a href="<?php echo esc_url(home_url('/foro/' . $forum->slug)); ?>"><?php echo esc_html($forum->name); ?></a>
            <span class="sep">›</span>
        <?php endif; ?>
        <span class="current"><?php echo esc_html($topic->title); ?></span>
    </nav>

    <!-- Page Grid: main + sidebar -->
    <div class="topic-page-grid">

        <!-- ─── Main Column ─── -->
        <div>

            <!-- ══ THREAD ══ -->
            <div style="display:flex; flex-direction:column;">

                <!-- Original Post (#1) -->
                <div class="thread-post">
                    <div class="post-head">
                        <span class="post-date"><?php echo esc_html($topic->created_at); ?></span>
                        <span class="post-num">#1</span>
                    </div>
                    <div class="post-body">
                        <!-- Desktop author sidebar -->
                        <div class="post-author">
                            <a href="<?php echo esc_url(home_url('/perfil/' . $topic->user_id)); ?>">
                                <div class="av-lg"><?php echo get_avatar($topic->user_id, 64, '', '', ['echo'=>false]); ?></div>
                            </a>
                            <a href="<?php echo esc_url(home_url('/perfil/' . $topic->user_id)); ?>" class="pa-name">
                                <?php echo esc_html($topic->author_name); ?>
                            </a>
                            <span class="badge <?php echo FR_Helpers::get_level_badge_class($topic->author_level); ?>">
                                <?php echo ucfirst($topic->author_level); ?>
                            </span>
                        </div>
                        <!-- Mobile author -->
                        <div class="post-author-mobile">
                            <div class="av-sm"><?php echo get_avatar($topic->user_id, 34, '', '', ['echo'=>false]); ?></div>
                            <div>
                                <div class="pam-name"><?php echo esc_html($topic->author_name); ?></div>
                                <div class="pam-level"><?php echo ucfirst($topic->author_level); ?></div>
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="post-content">
                            <h1 class="thread-title"><?php echo esc_html($topic->title); ?></h1>
                            <div class="post-text"><?php echo wpautop(esc_html($topic->content)); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Replies -->
                <?php if ($replies) : ?>
                    <?php foreach ($replies as $idx => $reply) : ?>
                    <div class="thread-post" id="reply-<?php echo $reply->id; ?>">
                        <div class="post-head">
                            <span class="post-date"><?php echo esc_html($reply->created_at); ?></span>
                            <span class="post-num">#<?php echo $idx + 2; ?></span>
                        </div>
                        <div class="post-body">
                            <!-- Desktop author -->
                            <div class="post-author">
                                <div class="av-md"><?php echo get_avatar($reply->user_id, 48, '', '', ['echo'=>false]); ?></div>
                                <span class="pa-name"><?php echo esc_html($reply->author_name); ?></span>
                                <span class="badge <?php echo FR_Helpers::get_level_badge_class($reply->author_level); ?>">
                                    <?php echo ucfirst($reply->author_level); ?>
                                </span>
                            </div>
                            <!-- Mobile author -->
                            <div class="post-author-mobile">
                                <div class="av-sm"><?php echo get_avatar($reply->user_id, 34, '', '', ['echo'=>false]); ?></div>
                                <div>
                                    <div class="pam-name"><?php echo esc_html($reply->author_name); ?></div>
                                    <div class="pam-level"><?php echo ucfirst($reply->author_level); ?></div>
                                </div>
                            </div>
                            <!-- Content -->
                            <div class="post-content">
                                <div class="post-text"><?php echo wpautop(esc_html($reply->content)); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div><!-- end thread -->

            <!-- ══ REPLY FORM ══ -->
            <?php if (is_user_logged_in()) : ?>
            <div class="reply-section">
                <h3>Publicar respuesta</h3>
                <div class="reply-inner">
                    <div class="reply-avatar"><?php echo get_avatar(get_current_user_id(), 38, '', '', ['echo'=>false]); ?></div>
                    <div class="reply-form-group">
                        <form id="create-reply-form">
                            <input type="hidden" name="action"   value="fr_create_reply">
                            <input type="hidden" name="topic_id" value="<?php echo esc_attr($topic->id); ?>">
                            <textarea name="content" rows="4" required class="form-textarea" placeholder="Escribe tu respuesta aquí…"></textarea>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Responder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else : ?>
            <div class="login-prompt">
                <p><a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">Inicia sesión</a> para participar en la discusión.</p>
            </div>
            <?php endif; ?>

        </div><!-- end main column -->

        <!-- ─── Sidebar ─── -->
        <aside class="topic-page-sidebar sidebar" style="display:none;">
            <?php get_sidebar(); ?>
        </aside>

    </div><!-- end grid -->

</div>

<?php get_footer(); ?>
