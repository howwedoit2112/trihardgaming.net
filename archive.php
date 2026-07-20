<?php get_header(); ?>

<header class="archive-header">
    <?php the_archive_title( '<h1>', '</h1>' ); ?>
    <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
</header>

<div class="articles-grid">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            ?>
            <article class="article-card">
                <div class="card-header">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </a>
                    <?php endif; ?>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php the_excerpt(); ?></p>
                </div>
                <div class="card-meta">
                    <span class="tag"><?php the_category( ', ' ); ?></span>
                    <time datetime="<?php echo get_the_date( 'c' ); ?>"><?php the_date(); ?></time>
                </div>
            </article>
            <?php
        endwhile;
        
        the_posts_navigation();
    else :
        ?>
        <div class="no-posts">
            <p><?php _e( 'No posts found.', 'trihardgaming' ); ?></p>
        </div>
        <?php
    endif;
    ?>
</div>

<?php get_footer(); ?>