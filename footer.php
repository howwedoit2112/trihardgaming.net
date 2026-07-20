    </main>

    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-widgets">
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                <?php endif; ?>
            </div>
            <div class="site-info">
                <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php _e( 'All rights reserved.', 'trihardgaming' ); ?></p>
                <p><small><?php _e( 'Built with WordPress & GitHub Pages', 'trihardgaming' ); ?></small></p>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
