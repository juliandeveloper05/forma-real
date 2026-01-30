
</main> <!-- #primary -->

<footer class="site-footer bg-secondary text-white pt-12 pb-6">
    <div class="container">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Footer Area 1 -->
            <div class="footer-widget">
                <h3 class="text-lg font-bold mb-4">Sobre Forma Real</h3>
                <p class="text-gray-400 text-sm">
                    Comunidad de fitness real, sin filtros. Aprende, comparte y mejora tu salud con información basada en la experiencia.
                </p>
            </div>
            
            <!-- Footer Area 2 -->
            <div class="footer-widget">
                <h3 class="text-lg font-bold mb-4">Enlaces Rápidos</h3>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-links space-y-2 text-sm text-gray-400',
                    'container'      => false,
                ]);
                ?>
            </div>
            
            <!-- Footer Area 3 -->
            <div class="footer-widget">
                <h3 class="text-lg font-bold mb-4">Síguenos</h3>
                <div class="flex space-x-4">
                    <!-- Social Icons placeholder -->
                    <a href="#" class="text-gray-400 hover:text-white">Instagram</a>
                    <a href="#" class="text-gray-400 hover:text-white">Twitter</a>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-500">
            &copy; <?php echo date('Y'); ?> Forma Real. Todos los derechos reservados.
        </div>
        
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
