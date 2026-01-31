<?php
/**
 * Template Name: Search Results
 * Resultados de búsqueda del foro
 */

get_header();

$query = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
$page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

$search = new FR_Search();
$results = $search->search($query, $page);
?>

<div class="container py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Búsqueda</h1>
        
        <!-- Search Form -->
        <form action="<?php echo home_url('/buscar/'); ?>" method="GET" class="flex gap-4">
            <input 
                type="text" 
                name="q" 
                value="<?php echo esc_attr($query); ?>"
                placeholder="Buscar en el foro..."
                class="flex-grow px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none"
            >
            <button type="submit" class="btn btn-primary px-6">
                🔍 Buscar
            </button>
        </form>
    </div>

    <?php if ($query) : ?>
        <!-- Results Info -->
        <div class="mb-6 text-gray-600">
            <?php if ($results['total'] > 0) : ?>
                <p>Se encontraron <strong><?php echo $results['total']; ?></strong> resultados para "<strong><?php echo esc_html($query); ?></strong>"</p>
            <?php else : ?>
                <p>No se encontraron resultados para "<strong><?php echo esc_html($query); ?></strong>"</p>
            <?php endif; ?>
        </div>

        <!-- Results List -->
        <?php if ($results['total'] > 0) : ?>
            <div class="space-y-4">
                <?php foreach ($results['results'] as $result) : ?>
                    <div class="card p-4 hover:border-gray-300 transition-colors">
                        <div class="flex gap-4">
                            <!-- Type Badge -->
                            <div class="shrink-0">
                                <?php if ($result->result_type === 'topic') : ?>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">Tema</span>
                                <?php else : ?>
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Respuesta</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-grow min-w-0">
                                <?php if ($result->result_type === 'topic') : ?>
                                    <h3 class="text-lg font-semibold">
                                        <a href="<?php echo home_url('/foro/' . $result->forum_slug . '/' . $result->slug); ?>" class="text-gray-900 hover:text-primary">
                                            <?php echo esc_html($result->title); ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                        <?php echo wp_trim_words(strip_tags($result->content), 30); ?>
                                    </p>
                                <?php else : ?>
                                    <h3 class="text-lg font-semibold">
                                        <a href="<?php echo home_url('/foro/' . $result->forum_slug . '/' . $result->topic_slug . '#reply-' . $result->id); ?>" class="text-gray-900 hover:text-primary">
                                            Re: <?php echo esc_html($result->topic_title); ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                        <?php echo wp_trim_words(strip_tags($result->content), 30); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="text-sm text-gray-500 mt-2">
                                    por <span class="font-medium"><?php echo esc_html($result->author_name); ?></span>
                                    • <?php echo FR_Helpers::time_ago($result->created_at); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($results['pages'] > 1) : ?>
                <div class="flex justify-center gap-2 mt-8">
                    <?php for ($i = 1; $i <= $results['pages']; $i++) : ?>
                        <a 
                            href="<?php echo add_query_arg(['q' => $query, 'paged' => $i], home_url('/buscar/')); ?>"
                            class="px-4 py-2 rounded <?php echo $i === $page ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>"
                        >
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <!-- No Results -->
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron resultados</h3>
                <p class="text-gray-500">Intenta con otras palabras clave o revisa la ortografía.</p>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🔎</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">¿Qué estás buscando?</h3>
            <p class="text-gray-500">Escribe al menos 3 caracteres para buscar en el foro.</p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
