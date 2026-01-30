<?php
/**
 * Template Name: Topic Single
 * Muestra un tema individual y sus respuestas
 */

get_header();

// Obtener slugs
$forum_slug = get_query_var('forum_slug');
$topic_slug = get_query_var('topic_slug');

// Handlers
$forum_handler = new FR_Forum();
$topic_handler = new FR_Topic();
$reply_handler = new FR_Reply();

// Obtener datos
$forum = $forum_handler->get_by_slug($forum_slug);
// Nota: get_topic_full espera ID, necesitamos un get_by_slug en Handler o buscar ID primero.
// Por eficiencia, haremos query directa de ID por slug aquí o añadiremos método get_id_by_slug.
// Para este ejemplo asumimos que necesitamos buscar el ID.
global $wpdb;
$topic_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}fr_topics WHERE slug = %s", $topic_slug));

if (!$topic_id) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}

$topic = $topic_handler->get_topic_full($topic_id);
$replies = $reply_handler->get_by_topic($topic_id);

?>

<div class="container py-8">
    
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2 flex-wrap">
        <a href="<?php echo home_url('/foro/'); ?>" class="hover:text-primary">Foro</a>
        <span>&rsaquo;</span>
        <a href="<?php echo home_url('/foro/' . $forum->slug); ?>" class="hover:text-primary"><?php echo esc_html($forum->name); ?></a>
        <span>&rsaquo;</span>
        <span class="font-medium text-gray-900 truncate max-w-[200px]"><?php echo esc_html($topic->title); ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Main Content (Topic + Replies) -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- TOPIC POST -->
            <div class="card border-l-4 border-l-primary">
                <div class="card-header flex justify-between items-center bg-gray-50">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <span class="font-bold text-gray-900"><?php echo esc_html($topic->created_at); ?></span>
                        <?php if ($topic->is_sticky) : ?><span class="badge bg-yellow-100 text-yellow-800">Fijado</span><?php endif; ?>
                    </div>
                    <div class="text-sm text-gray-400">#1</div>
                </div>
                <div class="card-body flex gap-6">
                    <!-- Author Sidebar (Desktop) -->
                    <div class="hidden md:flex flex-col items-center w-32 shrink-0 text-center space-y-2">
                        <a href="<?php echo home_url('/perfil/' . $topic->user_id); ?>" class="block">
                            <?php echo get_avatar($topic->user_id, 80, '', '', ['class' => 'rounded-full border border-gray-200']); ?>
                        </a>
                        <a href="<?php echo home_url('/perfil/' . $topic->user_id); ?>" class="font-bold text-sm text-primary hover:underline">
                            <?php echo esc_html($topic->author_name); ?>
                        </a>
                        <span class="badge <?php echo FR_Helpers::get_level_badge_class($topic->author_level); ?>">
                            <?php echo ucfirst($topic->author_level); ?>
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="flex-grow min-w-0">
                        <!-- Mobile Author Header -->
                        <div class="md:hidden flex items-center gap-3 mb-4 border-b pb-3 border-gray-100">
                             <?php echo get_avatar($topic->user_id, 40, '', '', ['class' => 'rounded-full']); ?>
                             <div>
                                <div class="font-bold text-sm"><?php echo esc_html($topic->author_name); ?></div>
                                <div class="text-xs text-gray-500"><?php echo ucfirst($topic->author_level); ?></div>
                             </div>
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 mb-4"><?php echo esc_html($topic->title); ?></h1>
                        <div class="prose max-w-none text-gray-800">
                            <?php echo wpautop(esc_html($topic->content)); // Nota: En prod usar un sanitizador más permisivo para rich text ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REPLIES LOOP -->
            <?php if ($replies) : ?>
                <?php foreach ($replies as $index => $reply) : ?>
                    <div id="reply-<?php echo $reply->id; ?>" class="card">
                        <div class="card-header bg-white py-3 flex justify-between text-xs text-gray-500">
                            <span><?php echo esc_html($reply->created_at); ?></span>
                            <span>#<?php echo $index + 2; ?></span>
                        </div>
                        <div class="card-body flex gap-6 pt-0">
                            <!-- Author Sidebar -->
                            <div class="hidden md:flex flex-col items-center w-32 shrink-0 text-center space-y-2 pt-4">
                                <?php echo get_avatar($reply->user_id, 64, '', '', ['class' => 'rounded-full']); ?>
                                <span class="font-bold text-xs"><?php echo esc_html($reply->author_name); ?></span>
                                <span class="badge <?php echo FR_Helpers::get_level_badge_class($reply->author_level); ?> text-[10px]">
                                    <?php echo ucfirst($reply->author_level); ?>
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-grow min-w-0 pt-4">
                                <div class="prose max-w-none text-gray-800 text-sm">
                                    <?php echo wpautop(esc_html($reply->content)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- REPLY FORM -->
            <?php if (is_user_logged_in()) : ?>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mt-8">
                    <h3 class="text-lg font-bold mb-4">Publicar respuesta</h3>
                    <form id="create-reply-form">
                        <input type="hidden" name="action" value="fr_create_reply">
                        <input type="hidden" name="topic_id" value="<?php echo esc_attr($topic->id); ?>">
                        
                        <div class="flex gap-4">
                            <div class="shrink-0 hidden md:block">
                                <?php echo get_avatar(get_current_user_id(), 48, '', '', ['class' => 'rounded-lg']); ?>
                            </div>
                            <div class="flex-grow">
                                <textarea name="content" rows="4" required placeholder="Escribe tu respuesta aquí..." class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-primary focus:border-transparent outline-none"></textarea>
                                <div class="mt-2 flex justify-end">
                                    <button type="submit" class="btn btn-primary">Responder</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php else : ?>
                <div class="bg-blue-50 p-6 rounded-lg text-center border border-blue-100">
                    <p class="text-blue-800">
                        <a href="<?php echo wp_login_url(get_permalink()); ?>" class="font-bold hover:underline">Inicia sesión</a> para participar en la discusión.
                    </p>
                </div>
            <?php endif; ?>

        </div>

        <!-- Sidebar -->
        <div class="hidden lg:block">
            <?php get_sidebar(); ?>
        </div>

    </div>

</div>

<?php get_footer(); ?>
