<?php get_header(); ?>

<article class="single-post">
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-featured-image">
            <?php the_post_thumbnail( 'large' ); ?>
        </div>
    <?php endif; ?>
    
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="entry-meta">
            <span class="author">By <?php the_author(); ?></span>
            <time datetime="<?php echo get_the_date( 'c' ); ?>"><?php the_date(); ?></time>
            <span class="category"><?php the_category( ', ' ); ?></span>
        </div>
    </header>
    
    <div class="entry-content">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
    
    <footer class="entry-footer">
        <?php if ( get_the_tag_list() ) : ?>
            <div class="tags">
                <?php the_tag_list( '<span class="tags-label">Tags: </span>', ', ', '' ); ?>
            </div>
        <?php endif; ?>
        
        <div class="share-buttons">
            <span>Share:</span>
            <a href="https://twitter.com/share?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank">Twitter</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank">Facebook</a>
        </div>
    </footer>
</article>

<?php comments_template(); ?>
<?php get_footer(); ?>
