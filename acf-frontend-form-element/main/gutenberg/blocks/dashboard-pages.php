<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Dashboard_Pages') ) :

    class Dashboard_Pages
    {    

        public function render($attributes, $content, $block)
        {
           
            if ( ! is_user_logged_in() ) {
                return wp_login_form( array(
                    'echo' => false,
                ) );
            }

            $pages = get_posts( array(
                    'post_type' => 'dashboard_page',
                    
                    'numberposts' => -1,
                ) );

            ob_start();

            ?>
            <div class="fea-dashboard-pages">

                <?php if ( ! empty( $pages ) ) : ?>
                    <ul class="fea-dashboard-pages-list">
                        <?php foreach ( $pages as $page ) : ?>
                            <li class="fea-dashboard-page-item">
                                <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>">
                                    <?php echo esc_html( get_the_title( $page->ID ) ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php esc_html_e( 'No dashboard pages found.', 'frontend-admin' ); ?></p>
                <?php endif; ?>

            </div>
            <?php

            wp_localize_script( 'frontend-admin-form-editor-script', 'feaDashboardPages', array(
                'pages' => array_map( function( $page ) {
                    return array(
                        'id' => $page->ID,
                        'title' => $page->post_title,
                        'url' => get_permalink( $page->ID ),
                    );
                }, $pages ),

            ) );

            return ob_get_clean();
            
        }
    }

endif;