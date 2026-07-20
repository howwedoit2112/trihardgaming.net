<?php get_header(); ?>

<section class="hero">
    <h1><?php echo esc_html( get_theme_mod( 'hero_title', 'AI & Tech Insights' ) ); ?></h1>
    <p><?php echo esc_html( get_theme_mod( 'hero_description', 'Exploring the frontier of artificial intelligence and emerging technologies.' ) ); ?></p>
    <a href="<?php echo home_url( '/blog' ); ?>" class="btn">Read Latest Posts</a>
</section>

<div class="articles-grid">
    <?php
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'meta_key'       => '_post_status',
        'meta_value'     => 'published',
    );
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
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
        wp_reset_postdata();
    endif;
    ?>
</div>

<section class="newsletter-form">
    <h3>Stay Updated</h3>
    <p>Get the latest AI and tech insights delivered to your inbox.</p>
    <form action="<?php echo home_url( '/subscribe' ); ?>" method="post">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Subscribe</button>
    </form>
</section>

<?php get_footer(); ?>
