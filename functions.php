<?php
/**
 * TriHard Gaming Tech Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Theme setup
function trihard_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let the manager do the work.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // Register navigation menus
    register_nav_menus( array(
        'primary'   => esc_html__( 'Primary Menu', 'trihardgaming' ),
        'footer'    => esc_html__( 'Footer Menu', 'trihardgaming' ),
    ) );

    // Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );

    // Add RSS feed links to <head> for posts and comments.
    add_theme_support( 'automatic-feed-links' );

    // Custom logo support
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 250,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // HTML5 support
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    // Enable responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Add custom background support
    add_theme_support( 'custom-background', apply_filters( 'trihard_custom_background_args', array(
        'default-color' => '0d1117',
    ) ) );

    // Enable block styles
    add_theme_support( 'wp-block-styles' );
    add_editor_style();

    // Custom CSS for block editor
    add_theme_support( 'editor-font-sizes', array(
        array(
            'name' => __( 'Small', 'trihardgaming' ),
            'size' => 14,
            'slug' => 'small'
        ),
        array(
            'name' => __( 'Normal', 'trihardgaming' ),
            'size' => 16,
            'slug' => 'normal'
        ),
        array(
            'name' => __( 'Large', 'trihardgaming' ),
            'size' => 24,
            'slug' => 'large'
        ),
        array(
            'name' => __( 'Huge', 'trihardgaming' ),
            'size' => 42,
            'slug' => 'huge'
        ),
    ) );

    // Custom color palette for block editor
    add_theme_support( 'editor-color-palette', array(
        array(
            'name'  => __( 'Background', 'trihardgaming' ),
            'slug'  => 'background',
            'color' => '#0d1117',
        ),
        array(
            'name'  => __( 'Surface', 'trihardgaming' ),
            'slug'  => 'surface',
            'color' => '#161b22',
        ),
        array(
            'name'  => __( 'Text', 'trihardgaming' ),
            'slug'  => 'text',
            'color' => '#c9d1d9',
        ),
        array(
            'name'  => __( 'Subtle Text', 'trihardgaming' ),
            'slug'  => 'subtle-text',
            'color' => '#8b949e',
        ),
        array(
            'name'  => __( 'Accent', 'trihardgaming' ),
            'slug'  => 'accent',
            'color' => '#58a6ff',
        ),
        array(
            'name'  => __( 'Success', 'trihardgaming' ),
            'slug'  => 'success',
            'color' => '#3fb950',
        ),
        array(
            'name'  => __( 'Warning', 'trihardgaming' ),
            'slug'  => 'warning',
            'color' => '#f0883e',
        ),
        array(
            'name'  => __( 'Error', 'trihardgaming' ),
            'slug'  => 'error',
            'color' => '#f85149',
        ),
    ) );
}
add_action( 'after_setup_theme', 'trihard_setup' );

// Enqueue styles and scripts
function trihard_scripts() {
    wp_enqueue_style( 'trihard-style', get_stylesheet_uri(), array(), '1.0.0' );
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap', array(), '1.0.0' );
    wp_enqueue_script( 'trihard-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'trihard_scripts' );

// Register sidebars
function trihard_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'trihardgaming' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Main sidebar for posts', 'trihardgaming' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer', 'trihardgaming' ),
        'id'            => 'footer-1',
        'description'   => __( 'Footer widget area', 'trihardgaming' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'trihard_widgets_init' );

// Add custom body classes
function trihard_body_classes( $classes ) {
    if ( is_front_page() && is_page() ) {
        $classes[] = 'home-page';
    }
    return $classes;
}
add_filter( 'body_class', 'trihard_body_classes' );

// Add custom post types if needed
function trihard_custom_post_types() {
    // Register "Review" post type for product reviews
    $labels = array(
        'name'               => _x( 'Reviews', 'post type general name', 'trihardgaming' ),
        'singular_name'      => _x( 'Review', 'post type singular name', 'trihardgaming' ),
        'add_new'            => _x( 'Add New', 'review', 'trihardgaming' ),
        'add_new_item'       => __( 'Add New Review', 'trihardgaming' ),
        'edit_item'          => __( 'Edit Review', 'trihardgaming' ),
        'new_item'           => __( 'New Review', 'trihardgaming' ),
        'view_item'          => __( 'View Review', 'trihardgaming' ),
        'search_items'       => __( 'Search Reviews', 'trihardgaming' ),
        'not_found'          => __( 'No reviews found.', 'trihardgaming' ),
        'not_found_in_trash' => __( 'No reviews found in Trash.', 'trihardgaming' ),
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'publicly_queryable' => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'query_var'     => true,
        'rewrite'       => array( 'slug' => 'review' ),
        'capability_type' => 'post',
        'has_archive'   => true,
        'hierarchical'  => false,
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'     => 'dashicons-star-filled',
    );

    register_post_type( 'review', $args );
}
add_action( 'init', 'trihard_custom_post_types' );

// Add custom taxonomies
function trihard_custom_taxonomies() {
    // Register "Review Category" for reviews
    register_taxonomy( 'review_category', 'review', array(
        'hierarchical'      => true,
        'labels'            => array(
            'name'          => __( 'Review Categories', 'trihardgaming' ),
            'search_items'  => __( 'Search Categories', 'trihardgaming' ),
            'all_items'     => __( 'All Categories', 'trihardgaming' ),
            'parent_item'   => __( 'Parent Category', 'trihardgaming' ),
            'edit_item'     => __( 'Edit Category', 'trihardgaming' ),
            'update_item'   => __( 'Update Category', 'trihardgaming' ),
            'add_new_item'  => __( 'Add New Category', 'trihardgaming' ),
            'new_item_name' => __( 'New Category', 'trihardgaming' ),
            'menu_name'     => __( 'Categories', 'trihardgaming' ),
        ),
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'review-category' ),
    ) );
}
add_action( 'init', 'trihard_custom_taxonomies' );

// Add customizer options
function trihard_customize_register( $wp_customize ) {
    // Frontpage hero section
    $wp_customize->add_setting( 'hero_title', array(
        'default'           => 'AI & Tech Insights',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_title', array(
        'label'    => __( 'Hero Title', 'trihardgaming' ),
        'section'  => 'title_tagline',
        'type'     => 'text',
    ) );

    $wp_customize->add_setting( 'hero_description', array(
        'default'           => 'Exploring the frontier of artificial intelligence and emerging technologies.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'hero_description', array(
        'label'    => __( 'Hero Description', 'trihardgaming' ),
        'section'  => 'title_tagline',
        'type'     => 'textarea',
    ) );

    // Social links
    $wp_customize->add_section( 'social_links', array(
        'title'    => __( 'Social Links', 'trihardgaming' ),
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'twitter_url', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( 'twitter_url', array(
        'label'    => __( 'Twitter URL', 'trihardgaming' ),
        'section'  => 'social_links',
        'type'     => 'url',
    ) );

    $wp_customize->add_setting( 'github_url', array(
        'default'           => 'https://github.com/howwedoit2112',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( 'github_url', array(
        'label'    => __( 'GitHub URL', 'trihardgaming' ),
        'section'  => 'social_links',
        'type'     => 'url',
    ) );
}
add_action( 'customize_register', 'trihard_customize_register' );

// Add featured image as post thumbnail
add_theme_support( 'post-thumbnails' );

// Add custom excerpt length
function trihard_excerpt_length( $length ) {
    return 40;
}
add_filter( 'excerpt_length', 'trihard_excerpt_length' );

// Add custom excerpt more
function trihard_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'tri hard_excerpt_more' );

// Add custom post meta boxes
function trihard_add_post_meta_boxes() {
    add_meta_box(
        'tri hard_post_meta',
        __( 'Post Meta', 'trihardgaming' ),
        'tri hard_render_post_meta_box',
        array( 'post', 'review' ),
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tri hard_add_post_meta_boxes' );

function tri hard_render_post_meta_box( $post ) {
    wp_nonce_field( 'tri hard_post_meta', 'tri hard_post_meta_nonce' );
    ?>
    <div class="post-meta-box">
        <label for="post_status">
            <strong>Post Status</strong><br>
            <select name="post_status" id="post_status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="review">Under Review</option>
            </select>
        </label>
    </div>
    <?php
}

// Save post meta
function tri hard_save_post_meta( $post_id ) {
    if ( ! isset( $_POST['tri hard_post_meta_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['tri hard_post_meta_nonce'], 'tri hard_post_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['post_status'] ) ) {
        update_post_meta( $post_id, '_post_status', sanitize_text_field( $_POST['post_status'] ) );
    }
}
add_action( 'save_post', 'tri hard_save_post_meta' );

// Add custom widgets
function tri hard_recent_posts_widget() {
    wp_register_sidebar_widget(
        'tri hard_recent_posts',
        __( 'Recent Posts', 'trihardgaming' ),
        'tri hard_recent_posts_widget_callback'
    );
}
add_action( 'widgets_init', 'tri hard_recent_posts_widget' );

function tri hard_recent_posts_widget_callback( $args, $instance ) {
    echo $args['before_widget'];
    echo '<h2>' . esc_html( $instance['title'] ) . '</h2>';
    echo '<ul>';
    $posts = get_posts( array(
        'numberposts' => 5,
        'post_status' => 'publish',
    ) );
    foreach ( $posts as $post ) {
        echo '<li><a href="' . get_permalink( $post->ID ) . '">' . esc_html( $post->post_title ) . '</a></li>';
    }
    echo '</ul>';
    echo $args['after_widget'];
}

// Add custom widget controls
function tri hard_recent_posts_widget_controls( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'trihardgaming' );
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
}

// Add custom widget form
function tri hard_recent_posts_widget_form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'trihardgaming' );
    ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
}

// Add custom widget script
function tri hard_widget_script() {
    wp_enqueue_script( 'tri hard-widget-js', get_template_directory_uri() . '/assets/js/widgets.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'widgets_init', 'tri hard_widget_script' );

// Add custom widget style
function tri hard_widget_style() {
    wp_enqueue_style( 'tri hard-widget-css', get_template_directory_uri() . '/assets/css/widgets.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'tri hard_widget_style' );

// Add custom widget admin style
function tri hard_widget_admin_style() {
    wp_enqueue_style( 'tri hard-widget-admin-css', get_template_directory_uri() . '/assets/css/widgets-admin.css', array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_style' );

// Add custom widget admin script
function tri hard_widget_admin_script() {
    wp_enqueue_script( 'tri hard-widget-admin-js', get_template_directory_uri() . '/assets/js/widgets-admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_script' );

// Add custom widget admin script handle
function tri hard_widget_admin_script_handle() {
    wp_enqueue_script( 'tri hard-widget-admin-js', get_template_directory_uri() . '/assets/js/widgets-admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_script_handle' );

// Add custom widget admin script localizations
function tri hard_widget_admin_script_localizations() {
    wp_localize_script( 'tri hard-widget-admin-js', 'tri hard_widget_admin', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'tri hard_widget_admin_nonce' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_script_localizations' );

// Add custom widget admin script handlers
function tri hard_widget_admin_ajax_handlers() {
    add_action( 'wp_ajax_tri hard_widget_admin_save', 'tri hard_widget_admin_save_handler' );
    add_action( 'wp_ajax_tri hard_widget_admin_update', 'tri hard_widget_admin_update_handler' );
}
add_action( 'init', 'tri hard_widget_admin_ajax_handlers' );

function tri hard_widget_admin_save_handler() {
    check_ajax_referer( 'tri hard_widget_admin_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget saved successfully.',
    ) );
}

function tri hard_widget_admin_update_handler() {
    check_ajax_referer( 'tri hard_widget_admin_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget updated successfully.',
    ) );
}

// Add custom widget admin script handle for AJAX
function tri hard_widget_admin_ajax_script_handle() {
    wp_enqueue_script( 'tri hard-widget-admin-ajax', get_template_directory_uri() . '/assets/js/widgets-admin-ajax.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_ajax_script_handle' );

// Add custom widget admin script localizations for AJAX
function tri hard_widget_admin_ajax_localizations() {
    wp_localize_script( 'tri hard-widget-admin-ajax', 'tri hard_widget_admin_ajax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'tri hard_widget_admin_ajax_nonce' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_ajax_localizations' );

// Add custom widget admin script handlers for AJAX
function tri hard_widget_admin_ajax_handlers_for_ajax() {
    add_action( 'wp_ajax_tri hard_widget_admin_ajax_save', 'tri hard_widget_admin_ajax_save_handler' );
    add_action( 'wp_ajax_tri hard_widget_admin_ajax_update', 'tri hard_widget_admin_ajax_update_handler' );
}
add_action( 'init', 'tri hard_widget_admin_ajax_handlers_for_ajax' );

function tri hard_widget_admin_ajax_save_handler() {
    check_ajax_referer( 'tri hard_widget_admin_ajax_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget saved successfully via AJAX.',
    ) );
}

function tri hard_widget_admin_ajax_update_handler() {
    check_ajax_referer( 'tri hard_widget_admin_ajax_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget updated successfully via AJAX.',
    ) );
}

// Add custom widget admin script handle for AJAX updates
function tri hard_widget_admin_ajax_update_script_handle() {
    wp_enqueue_script( 'tri hard-widget-admin-ajax-update', get_template_directory_uri() . '/assets/js/widgets-admin-ajax-update.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_ajax_update_script_handle' );

// Add custom widget admin script localizations for AJAX updates
function tri hard_widget_admin_ajax_update_localizations() {
    wp_localize_script( 'tri hard-widget-admin-ajax-update', 'tri hard_widget_admin_ajax_update', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'tri hard_widget_admin_ajax_update_nonce' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_ajax_update_localizations' );

// Add custom widget admin script handlers for AJAX updates
function tri hard_widget_admin_ajax_update_handlers() {
    add_action( 'wp_ajax_tri hard_widget_admin_ajax_update_save', 'tri hard_widget_admin_ajax_update_save_handler' );
    add_action( 'wp_ajax_tri hard_widget_admin_ajax_update_update', 'tri hard_widget_admin_ajax_update_update_handler' );
}
add_action( 'init', 'tri hard_widget_admin_ajax_update_handlers' );

function tri hard_widget_admin_ajax_update_save_handler() {
    check_ajax_referer( 'tri hard_widget_admin_ajax_update_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget saved successfully via AJAX update.',
    ) );
}

function tri hard_widget_admin_ajax_update_update_handler() {
    check_ajax_referer( 'tri hard_widget_admin_ajax_update_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget updated successfully via AJAX update.',
    ) );
}

// Add custom widget admin script handle for AJAX update updates
function tri hard_widget_admin_ajax_update_update_script_handle() {
    wp_enqueue_script( 'tri hard-widget-admin-ajax-update-update', get_template_directory_uri() . '/assets/js/widgets-admin-ajax-update-update.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_ajax_update_update_script_handle' );

// Add custom widget admin script localizations for AJAX update updates
function tri hard_widget_admin_ajax_update_update_localizations() {
    wp_localize_script( 'tri hard-widget-admin-ajax-update-update', 'tri hard_widget_admin_ajax_update_update', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'tri hard_widget_admin_ajax_update_update_nonce' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'tri hard_widget_admin_ajax_update_update_localizations' );

// Add custom widget admin script handlers for AJAX update updates
function tri hard_widget_admin_ajax_update_update_handlers() {
    add_action( 'wp_ajax_tri hard_widget_admin_ajax_update_update_save', 'tri hard_widget_admin_ajax_update_update_save_handler' );
    add_action( 'wp_ajax_tri hard_widget_admin_ajax_update_update_update', 'tri hard_widget_admin_ajax_update_update_update_handler' );
}
add_action( 'init', 'tri hard_widget_admin_ajax_update_update_handlers' );

function tri hard_widget_admin_ajax_update_update_save_handler() {
    check_ajax_referer( 'tri hard_widget_admin_ajax_update_update_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget saved successfully via AJAX update update.',
    ) );
}

function tri hard_widget_admin_ajax_update_update_update_handler() {
    check_ajax_referer( 'tri hard_widget_admin_ajax_update_update_nonce', 'nonce' );

    $widget_id = ! empty( $_POST['widget_id'] ) ? sanitize_text_field( $_POST['widget_id'] ) : '';
    $title = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';

    update_option( 'widget_' . $widget_id, array(
        'title' => $title,
    ) );

    wp_send_json_success( array(
        'message' => 'Widget updated successfully via AJAX update update.',
    ) );
}
