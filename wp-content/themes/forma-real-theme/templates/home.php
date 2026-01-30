<?php
/**
 * Template Name: Home Page Landing
 */

get_header(); 

// Obtener últimos temas destacados para la home
$topic_handler = new FR_Topic();
$recent_topics = $topic_handler->get_recent_topics(5);
?>

<!-- Hero Section -->
<section class="bg-white border-b border-gray-200 py-16 md:py-24">
    <div class="container text-center max-w-3xl">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-6">
            Fitness Real, Resultados Reales.
        </h1>
        <p class="text-lg text-gray-600 mb-8">
            Una comunidad donde la experiencia supera a la teoría. Comparte tus rutinas, resuelve dudas y documenta tu progreso sin filtros.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo home_url('/foro/'); ?>" class="btn btn-primary btn-lg px-8 py-3 text-lg">
                Ir al Foro
            </a>
            <?php if (!is_user_logged_in()) : ?>
                <a href="<?php echo wp_registration_url(); ?>" class="btn btn-outline btn-lg px-8 py-3 text-lg">
                    Unirse ahora
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Recent Activity Section -->
<section class="py-16 bg-gray-50">
    <div class="container">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Actividad Reciente</h2>
                <p class="text-gray-500">Lo que está pasando ahora mismo en la comunidad.</p>
            </div>
            <a href="<?php echo home_url('/foro/'); ?>" class="text-primary font-medium hover:underline">Ver todo &rarr;</a>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <?php if ($recent_topics) : ?>
                <?php foreach ($recent_topics as $topic) : ?>
                    <div class="card p-4 hover:border-gray-300 transition-colors">
                        <div class="flex gap-4">
                            <!-- Avatar -->
                            <div class="shrink-0">
                                <?php echo get_avatar($topic->user_id, 48, '', '', ['class' => 'rounded-full']); ?>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-grow min-w-0">
                                <h3 class="text-lg font-semibold truncate">
                                    <a href="<?php echo home_url('/foro/' . $topic->forum_slug . '/' . $topic->slug); ?>" class="text-gray-900 hover:text-primary">
                                        <?php echo esc_html($topic->title); ?>
                                    </a>
                                </h3>
                                <div class="text-sm text-gray-500 flex flex-wrap gap-2 items-center mt-1">
                                    <span>por <span class="font-medium text-gray-700"><?php echo esc_html($topic->author_name); ?></span></span>
                                    <span>&bull;</span>
                                    <span>en <a href="<?php echo home_url('/foro/' . $topic->forum_slug); ?>" class="text-gray-700 hover:underline"><?php echo esc_html($topic->forum_name); ?></a></span>
                                    <span>&bull;</span>
                                    <span><?php echo FR_Helpers::time_ago($topic->created_at); ?></span>
                                </div>
                            </div>

                            <!-- Meta -->
                            <div class="hidden sm:flex flex-col items-end justify-center text-sm text-gray-400 px-4 border-l border-gray-100">
                                <div class="font-bold text-gray-600"><?php echo number_format($topic->reply_count); ?></div>
                                <div class="text-xs">respuestas</div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="text-center py-8 text-gray-500">
                    Aún no hay actividad reciente. ¡Sé el primero en publicar!
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
