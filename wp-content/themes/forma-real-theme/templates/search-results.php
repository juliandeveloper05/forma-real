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

<div class="container" style="padding-top: 2.5rem; padding-bottom: 4rem;">

    <!-- Search Header -->
    <div class="section-head" style="margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: clamp(1.8rem, 4vw, 2.4rem);">🔍 Buscar en el Foro</h1>
            <?php if ($query) : ?>
                <p class="sub">Resultados para: <strong><?php echo esc_html($query); ?></strong></p>
            <?php else : ?>
                <p class="sub">Encuentra temas, respuestas y discusiones</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Search Form -->
    <div class="card" style="padding: 1.75rem; margin-bottom: 2.5rem; border-radius: var(--r-xl); background: linear-gradient(135deg, var(--color-primary-subtle) 0%, transparent 100%);">
        <form action="<?php echo home_url('/buscar/'); ?>" method="GET" style="display: flex; gap: 0.75rem;">
            <input 
                type="text" 
                name="q" 
                value="<?php echo esc_attr($query); ?>"
                placeholder="¿Qué estás buscando? Escribe al menos 3 caracteres..."
                class="form-input"
                style="flex-grow: 1; padding: 0.85rem 1.25rem; font-size: 0.95rem; border: 2px solid var(--color-border); background: var(--color-card);"
                autofocus
            >
            <button type="submit" class="btn btn-primary" style="padding: 0.85rem 1.75rem; white-space: nowrap;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; vertical-align: middle; margin-right: 0.4rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Buscar
            </button>
        </form>
    </div>

    <?php if ($query) : ?>
        
        <?php if ($results['total'] > 0) : ?>
            
            <!-- Results Stats -->
            <div style="margin-bottom: 1.5rem; padding: 0.75rem 1rem; background: var(--color-success-bg); border-radius: var(--r-md); border-left: 3px solid var(--color-success);">
                <p style="color: var(--color-success-text); font-size: 0.88rem; margin: 0;">
                    <strong><?php echo number_format($results['total']); ?></strong> resultado<?php echo $results['total'] > 1 ? 's' : ''; ?> encontrado<?php echo $results['total'] > 1 ? 's' : ''; ?>
                </p>
            </div>

            <!-- Results List -->
            <div class="space-y-3">
                <?php foreach ($results['results'] as $result) : ?>
                    <div class="card anim-up" style="padding: 0; border-radius: var(--r-lg); overflow: hidden; transition: all var(--ease-slow);">
                        <div style="display: flex; gap: 0; border-left: 3px solid <?php echo $result->result_type === 'topic' ? 'var(--color-primary)' : 'var(--color-success)'; ?>;">
                            
                            <!-- Type Badge Column -->
                            <div style="flex-shrink: 0; width: 70px; background: <?php echo $result->result_type === 'topic' ? 'var(--color-primary-subtle)' : 'var(--color-success-bg)'; ?>; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem;">
                                <span style="font-size: 1.5rem; margin-bottom: 0.3rem;">
                                    <?php echo $result->result_type === 'topic' ? '📄' : '💬'; ?>
                                </span>
                                <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: <?php echo $result->result_type === 'topic' ? 'var(--color-primary)' : 'var(--color-success-text)'; ?>;">
                                    <?php echo $result->result_type === 'topic' ? 'Tema' : 'Respuesta'; ?>
                                </span>
                            </div>
                            
                            <!-- Content -->
                            <div style="flex-grow: 1; padding: 1.25rem 1.5rem;">
                                <?php if ($result->result_type === 'topic') : ?>
                                    <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 0.5rem;">
                                        <a href="<?php echo home_url('/foro/' . $result->forum_slug . '/' . $result->slug); ?>" style="color: var(--color-text); transition: color var(--ease);">
                                            <?php echo esc_html($result->title); ?>
                                        </a>
                                    </h3>
                                    <p style="color: var(--color-text-2); font-size: 0.85rem; line-height: 1.6; margin-bottom: 0.65rem;">
                                        <?php echo wp_trim_words(strip_tags($result->content), 35); ?>
                                    </p>
                                <?php else : ?>
                                    <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 0.5rem;">
                                        <a href="<?php echo home_url('/foro/' . $result->forum_slug . '/' . $result->topic_slug . '#reply-' . $result->id); ?>" style="color: var(--color-text); transition: color var(--ease);">
                                            Re: <?php echo esc_html($result->topic_title); ?>
                                        </a>
                                    </h3>
                                    <p style="color: var(--color-text-2); font-size: 0.85rem; line-height: 1.6; margin-bottom: 0.65rem;">
                                        <?php echo wp_trim_words(strip_tags($result->content), 35); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div style="display: flex; align-items: center; gap: 0.55rem; font-size: 0.75rem; color: var(--color-text-muted);">
                                    <span>por <strong style="color: var(--color-text-2); font-weight: 600;"><?php echo esc_html($result->author_name); ?></strong></span>
                                    <span class="dot"></span>
                                    <span><?php echo FR_Helpers::time_ago($result->created_at); ?></span>
                                    <?php if ($result->result_type === 'topic') : ?>
                                        <span class="dot"></span>
                                        <span style="color: var(--color-primary); font-weight: 500;"><?php echo number_format($result->reply_count ?? 0); ?> respuestas</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($results['pages'] > 1) : ?>
                <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2.5rem;">
                    <?php for ($i = 1; $i <= min($results['pages'], 10); $i++) : ?>
                        <a 
                            href="<?php echo add_query_arg(['q' => $query, 'paged' => $i], home_url('/buscar/')); ?>"
                            class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>"
                            style="min-width: 42px; padding: 0.5rem 0.85rem;"
                        >
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            
            <!-- No Results -->
            <div class="empty-state" style="padding: 4rem 2rem;">
                <span class="empty-icon" style="font-size: 3.5rem; display: block; margin-bottom: 1rem;">🔍</span>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">No se encontraron resultados</h3>
                <p style="max-width: 400px; margin: 0 auto 1.5rem;">
                    No encontramos nada para "<strong><?php echo esc_html($query); ?></strong>". 
                    Intenta con otras palabras clave o verifica la ortografía.
                </p>
                <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="<?php echo home_url('/foro/'); ?>" class="btn btn-primary">Explorar el Foro</a>
                    <a href="<?php echo home_url('/buscar/'); ?>" class="btn btn-outline">Nueva Búsqueda</a>
                </div>
            </div>
            
        <?php endif; ?>

    <?php else : ?>
        
        <!-- Empty State - No Query -->
        <div class="empty-state" style="padding: 4rem 2rem;">
            <span class="empty-icon" style="font-size: 3.5rem; display: block; margin-bottom: 1rem;">🔎</span>
            <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">¿Qué estás buscando?</h3>
            <p style="max-width: 420px; margin: 0 auto 1.5rem;">
                Busca temas, respuestas y discusiones en toda la comunidad. 
                Escribe al menos 3 caracteres para comenzar.
            </p>
            <div style="display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap; font-size: 0.8rem;">
                <span style="padding: 0.35rem 0.75rem; background: var(--color-border-light); border-radius: var(--r-full); color: var(--color-text-muted);">💪 rutina</span>
                <span style="padding: 0.35rem 0.75rem; background: var(--color-border-light); border-radius: var(--r-full); color: var(--color-text-muted);">🥗 dieta</span>
                <span style="padding: 0.35rem 0.75rem; background: var(--color-border-light); border-radius: var(--r-full); color: var(--color-text-muted);">💊 proteína</span>
                <span style="padding: 0.35rem 0.75rem; background: var(--color-border-light); border-radius: var(--r-full); color: var(--color-text-muted);">🎯 hipertrofia</span>
            </div>
        </div>
        
    <?php endif; ?>

</div>

<?php get_footer(); ?>
