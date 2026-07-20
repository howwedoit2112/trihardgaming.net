<aside class="sidebar">
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    <?php else : ?>
        <div class="sidebar-widget">
            <h2>Recent Posts</h2>
            <?php
            wp_get_archives( array(
                'type' => 'monthly',
                'limit' => 10,
            ) );
            ?>
        </div>
        
        <div class="sidebar-widget">
            <h2>Categories</h2>
            <?php
            wp_list_categories( array(
                'title_li' => '',
                'taxonomy' => 'category',
            ) );
            ?>
        </div>
    <?php endif; ?>
</aside>
