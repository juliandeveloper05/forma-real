<?php
/**
 * Template Name: Forum Index
 * Muestra la lista de foros/categorías principales
 */

get_header(); 

// Instanciar controlador de foros
$forum_handler = new FR_Forum();
$forums = $forum_handler->get_all_forums();
?>

<div class="container py-8">
    
    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-900">Foros de Discusión</h1>
        <p class="text-gray-500 mt-2">Únete a la conversación sobre entrenamiento real y nutrición.</p>
    </div>

    <!-- Lista de Foros -->
    <div class="grid grid-cols-1 gap-6">
        <?php if ($forums) : ?>
            <?php foreach ($forums as $forum) : ?>
                <div class="card hover:shadow-md transition-shadow">
                    <div class="card-body flex items-start gap-4">
                        
                        <!-- Icono -->
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center shrink-0" 
                             style="background-color: <?php echo esc_attr($forum->color); ?>20; color: <?php echo esc_attr($forum->color); ?>">
                            <span class="text-2xl">
                                <?php echo !empty($forum->icon) ? esc_html($forum->icon) : '💬'; ?>
                            </span>
                        </div>
                        
                        <!-- Contenido -->
                        <div class="flex-grow">
                            <h2 class="text-xl font-bold mb-1">
                                <a href="<?php echo home_url('/foro/' . $forum->slug); ?>" class="text-gray-900 hover:text-primary">
                                    <?php echo esc_html($forum->name); ?>
                                </a>
                            </h2>
                            <p class="text-gray-600 text-sm mb-3">
                                <?php echo esc_html($forum->description); ?>
                            </p>
                            
                            <!-- Stats -->
                            <div class="flex gap-4 text-xs text-gray-400 font-medium">
                                <span class="flex items-center gap-1">
                                    📄 <?php echo number_format($forum->topic_count); ?> temas
                                </span>
                                <span class="flex items-center gap-1">
                                    💬 <?php echo number_format($forum->reply_count); ?> respuestas
                                </span>
                            </div>
                        </div>

                        <!-- Action (Desktop only) -->
                        <div class="hidden md:block self-center">
                            <a href="<?php echo home_url('/foro/' . $forum->slug); ?>" class="btn btn-outline btn-sm">
                                Entrar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="p-12 text-center border-2 border-dashed border-gray-200 rounded-lg">
                <p class="text-gray-400 text-lg">No hay foros creados todavía.</p>
                <?php if (current_user_can('manage_options')) : ?>
                    <p class="mt-2 text-sm text-gray-500">Ve a la base de datos o crea un admin seeder para añadir foros.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php get_footer(); ?>
