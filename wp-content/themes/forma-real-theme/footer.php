
</main><!-- #primary -->

<footer class="site-footer">
    <div class="container">

        <div class="footer-grid">

            <!-- Brand column -->
            <div class="footer-brand">
                <div class="fb-logo">Forma Real</div>
                <p>Comunidad de fitness real, sin filtros. Aprende, comparte y mejora tu salud con información basada en la experiencia real.</p>
                <div class="footer-social" style="margin-top:1rem;">
                    <a href="#">Instagram</a>
                    <a href="#">Twitter</a>
                    <a href="#">YouTube</a>
                </div>
            </div>

            <!-- Links -->
            <div class="footer-col">
                <h4>Explorar</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/foro/')); ?>">Foro</a></li>
                    <li><a href="<?php echo esc_url(home_url('/foro/rutinas')); ?>">Rutinas</a></li>
                    <li><a href="<?php echo esc_url(home_url('/foro/nutricion')); ?>">Nutrición</a></li>
                    <li><a href="<?php echo esc_url(home_url('/foro/suplementos')); ?>">Suplementos</a></li>
                    <li><a href="<?php echo esc_url(home_url('/foro/motivacion')); ?>">Motivación</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div class="footer-col">
                <h4>Recursos</h4>
                <ul>
                    <li><a href="<?php echo esc_url(wp_login_url()); ?>">Iniciar sesión</a></li>
                    <li><a href="<?php echo esc_url(wp_registration_url()); ?>">Registrarse</a></li>
                    <li><a href="#">Términos de uso</a></li>
                    <li><a href="#">Política de privacidad</a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> Forma Real. Todos los derechos reservados.</span>
            <span>Construido con WordPress</span>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
