<?php
/**
 * Template Name: Forum Category
 * Muestra los temas dentro de una categoría específica
 */

get_header();

// Obtener slug de la query var
$forum_slug = get_query_var('forum_slug');
$forum_handler = new FR_Forum();
$forum = $forum_handler->get_by_slug($forum_slug);

// Si no existe el foro, 404
if (!$forum) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}

// Obtener temas del foro
$topic_handler = new FR_Topic();
$page = get_query_var('paged') ? get_query_var('paged') : 1;
$topics = $topic_handler->get_by_forum($forum->id, $page);
?>

<div class="container py-8">
    
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
        <a href="<?php echo home_url('/foro/'); ?>" class="hover:text-primary">Foro</a>
        <span>&rsaquo;</span>
        <span class="font-medium text-gray-900"><?php echo esc_html($forum->name); ?></span>
    </nav>

    <!-- Header Categoría -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl flex items-center justify-center text-3xl shrink-0"
                 style="background-color: <?php echo esc_attr($forum->color); ?>20; color: <?php echo esc_attr($forum->color); ?>">
                <?php echo !empty($forum->icon) ? esc_html($forum->icon) : '📂'; ?>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1"><?php echo esc_html($forum->name); ?></h1>
                <p class="text-gray-600 text-sm"><?php echo esc_html($forum->description); ?></p>
            </div>
        </div>

        <?php if (is_user_logged_in()) : ?>
            <button onclick="document.getElementById('new-topic-modal').showModal()" class="btn btn-primary">
                + Nuevo Tema
            </button>
        <?php else : ?>
            <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn btn-outline">
                Inicia sesión para postear
            </a>
        <?php endif; ?>
    </div>

    <!-- Lista de Temas -->
    <div class="space-y-4">
        <?php if ($topics) : ?>
            <?php foreach ($topics as $topic) : ?>
                <div class="card p-4 hover:border-blue-300 transition-colors <?php echo $topic->is_sticky ? 'bg-blue-50 border-blue-200' : 'bg-white'; ?>">
                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                        
                        <!-- Estado Icon -->
                        <div class="shrink-0 text-gray-400 pt-1 md:pt-0">
                            <?php if ($topic->is_sticky) : ?>
                                <span title="Fijado">📌</span>
                            <?php elseif ($topic->is_closed) : ?>
                                <span title="Cerrado">🔒</span>
                            <?php else : ?>
                                <span title="Tema abierto">📄</span>
                            <?php endif; ?>
                        </div>

                        <!-- Info Principal -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold mb-1">
                                <a href="<?php echo home_url('/foro/' . $forum->slug . '/' . $topic->slug); ?>" class="text-gray-900 hover:text-primary">
                                    <?php echo esc_html($topic->title); ?>
                                </a>
                            </h3>
                            <div class="text-xs text-gray-500 flex flex-wrap gap-2">
                                <span>Iniciado por <strong><?php echo esc_html($topic->author_name); ?></strong></span>
                                <span>&bull;</span>
                                <span><?php echo FR_Helpers::time_ago($topic->created_at); ?></span>
                            </div>
                        </div>

                        <!-- Stats (Mobile Hidden usually, but let's show compact) -->
                        <div class="flex items-center gap-6 text-sm text-gray-500 shrink-0 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-2 md:pt-0 border-gray-100">
                            <div class="text-center">
                                <div class="font-bold text-gray-700"><?php echo number_format($topic->reply_count); ?></div>
                                <div class="text-xs">Respuestas</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-gray-700"><?php echo number_format($topic->view_count); ?></div>
                                <div class="text-xs">Vistas</div>
                            </div>
                            <div class="text-right min-w-[100px]">
                                <div class="text-xs text-gray-400">Última actividad</div>
                                <div class="font-medium text-gray-700 whitespace-nowrap">
                                    <?php echo FR_Helpers::time_ago($topic->last_active_time); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Pagination (Placeholder logic) -->
            <div class="pagination mt-8 flex justify-center gap-2">
                <!-- Implement pagination links -->
            </div>

        <?php else : ?>
            <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
                <div class="text-4xl mb-4">📭</div>
                <h3 class="text-lg font-medium text-gray-900">No hay temas en este foro</h3>
                <p class="text-gray-500">¡Sé el primero en iniciar una conversación!</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal Nuevo Tema (Simple HTML Dialog) -->
<?php if (is_user_logged_in()) : ?>
<dialog id="new-topic-modal" class="p-0 rounded-lg shadow-xl backdrop:bg-gray-900/50 w-full max-w-2xl">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Crear Nuevo Tema</h2>
            <button onclick="document.getElementById('new-topic-modal').close()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        
        <form id="create-topic-form" class="space-y-4">
            <input type="hidden" name="action" value="fr_create_topic">
            <input type="hidden" name="forum_id" value="<?php echo esc_attr($forum->id); ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                <input type="text" name="title" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                <textarea name="content" rows="6" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent outline-none"></textarea>
            </div>
            
            <div class="flex justify-end pt-4">
                <button type="button" onclick="document.getElementById('new-topic-modal').close()" class="btn btn-outline mr-2">Cancelar</button>
                <button type="submit" class="btn btn-primary">Publicar Tema</button>
            </div>
        </form>
    </div>
</dialog>
<?php endif; ?>

<?php get_footer(); ?>
