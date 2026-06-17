<?php
namespace Frontend_Admin\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Setup_Wizard' ) ) :


class Setup_Wizard {

    public function init() {

        $this->enqueue_assets();
        
     /*    add_action(
            'rest_api_init',
            [ $this, 'register_routes' ]
        ); */
    }

    public function enqueue_assets( $hook ) {
        

        wp_enqueue_script(
            'fea-setup-wizard',
            FEA_URL . 'assets/js/setup-wizard/index.js',
            [ 'wp-element', 'wp-components', 'wp-api-fetch' ],
            FEA_VERSION,
            true
        );

        wp_localize_script(
            'fea-setup-wizard',
            'feaSetup',
            [
                'restUrl' => rest_url(),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
            ]
        );
}

}

endif;