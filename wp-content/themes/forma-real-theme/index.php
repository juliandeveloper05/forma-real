<?php
/**
 * The main template file
 * 
 * This is the most generic template file in a WordPress theme.
 */

get_header(); ?>

<div class="container main-content">
    <div class="py-12 text-center">
        <?php if (have_posts()) : ?>
            
            <h1 class="text-3xl font-bold mb-8">Últimas entradas</h1>
            
            <div class="grid gap-6">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('card p-6 text-left'); ?>>
                        <h2 class="text-xl font-semibold mb-2">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="text-gray-600 mb-4">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="btn btn-outline">Leer más</a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination mt-8">
                <?php the_posts_navigation(); ?>
            </div>

        <?php else : ?>

            <h1 class="text-2xl font-bold text-gray-400">No se encontró contenido</h1>
            <p class="mt-4">Parece que no hay nada publicado todavía.</p>

        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
