<?php
namespace Frontend_Admin;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin_Gutenberg') ) :

    class Frontend_Admin_Gutenberg
    {

        public function register_blocks()
        {
            $blocks = [ 
                'form' => 'form',
                'admin-form' => 'admin_form',
                'submissions' => 'submissions',
            ];

            foreach( $blocks as $block => $name ){
                register_block_type(
                    FEA_DIR . "/assets/build/blocks/$block", [
                    'render_callback' => [ $this, 'render_' . $name ],
                    ] 
                );
            }

            $field_types = fea_instance()->frontend->field_types;
            

            if( $field_types ){
                foreach( $field_types as $type ){
                    
                    if ( $type instanceof Field_Types\Field_Base ) {
                        $name = str_replace( '_', '-', $type->name );
                        if( ! empty( $name ) && file_exists( FEA_DIR . "/assets/build/$name/index.js" ) ){
                            register_block_type(
                                FEA_DIR . "/assets/build/blocks/$name", [
                                'render_callback' => [ $this, 'render_field_block' ],
                                ] 
                            );        
                        }
                    }
                }
            }
        }

        public function render_field_block( $attr, $content, $block )
        {
            global $fea_instance, $fea_form;
            $form_display = $fea_instance->form_display;

            $render = '';        
            $field = acf_get_valid_field($attr);
            $field['builder'] = 'gutenberg';
            $field = $form_display->get_field_data_type( $field, $fea_form );
            
            $field['type'] = str_replace(
                array( 'frontend-admin/', '-field', '-' ),
                array( '', '', '_' ),
                $block->name
            );

            //fix options. They have to be an array of key => values, not array of index => [key,label]
            if( isset( $field['choices'] ) && is_array( $field['choices'] ) ){
                $choices = [];
                foreach( $field['choices'] as $key => $choice ){
                    if( is_array( $choice ) ){
                        $choices[ $choice['value'] ] = $choice['label'];
                    }else{
                        $choices[ $key ] = $choice;
                    }
                }
                $field['choices'] = $choices;
            }

            global $fea_form;



            ob_start();
            fea_instance()->form_display->render_field_wrap( $field );

            $render = ob_get_contents();
            ob_end_clean();    
            return $render;
        }
        
     

        public function render_form($attr, $content){
		
            $_settings = apply_filters( 'frontend_admin/show_form', $attr, 'form' );

            if( ! $_settings ) return;
            
            ob_start();
            global $fea_form, $fea_instance, $fea_scripts;

            $fea_form =  $fea_instance->form_display->validate_form( $attr );
            $GLOBALS['admin_form'] = $fea_form;


           /*  echo '<pre>';
            print_r($fea_form);
            echo '</pre>'; */
            do_action( 'frontend_admin/gutenberg/before_render', $fea_form );

            $fea_instance->frontend->enqueue_scripts( 'frontend_admin_form' );
            $fea_scripts = true;


            echo '<form '. feadmin_get_esc_attrs( $fea_form['form_attributes'] ) .'>';
            echo $content;
            $fea_instance->form_display->form_render_data( $fea_form );
            echo '</form>';

            do_action( 'frontend_admin/gutenberg/after_render', $fea_form );
            return ob_get_clean();

        }


        public function render_admin_form($attr, $content)
        {
            $render = '';
            if ($attr['formID'] == 0 ) {
                   return $render;
            }
            if (get_post_type($attr['formID']) == 'admin_form' ) {
                ob_start();
                if(is_admin() ) {
                    $attr['editMode'] = true;
                }else{
                    $attr['editMode'] = false;
                }
                fea_instance()->form_display->render_form($attr['formID'], $attr['editMode']);
                $render = ob_get_contents();
                ob_end_clean();    
            }
            return $render;
        }
        public function render_submissions($attr, $content)
        {
            $render = '';
            if ($attr['formID'] == 0 ) {
                return $render;
            }
            if (get_post_type($attr['formID']) == 'admin_form' ) {
                ob_start();
                if(is_admin() ) { $attr['editMode'] = true;
                }
                fea_instance()->form_display->render_submissions($attr['formID'], $attr['editMode']);
                $render = ob_get_contents();
                ob_end_clean();    
            }
            if(! $render ) {
                return __('No Submissions Found', 'acf-frontend-form-element');
            }
            return $render;
        }

        function add_block_categories( $block_categories )
        {
            return array_merge(
                $block_categories,
                [
                [
                'slug'  => 'frontend-admin',
                'title' => 'Frontend Admin',
                'icon'  => 'feedback', 
                ],
                ]
            );
        }
        /**
         *  enqueue_block_editor_assets
         *
         *  Allows a safe way to customize Guten-only functionality.
         *
         * @date  14/11/22
         * @since 5.8.0
         *
         * @param  void
         * @return void
         */
        function enqueue_block_editor_assets()
        {
            // Load the compiled blocks into the editor.
            wp_enqueue_script(
                'fea-dynamic-values',
                FEA_URL . '/assets/build/dynamic-values/index.js',
                ['wp-edit-post'],
                '1.0',
                true
            );
        }

        public function __construct()
        {
            add_filter('block_categories_all', array( $this, 'add_block_categories' ));
            add_action('init', array( $this, 'register_blocks' ), 20);

            //add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
            add_filter( 'render_block', function( $block_content, $block ) {                
                if ( $block['blockName'] === 'core/button' ) {
                    // Parse attributes if needed
                    $url = isset( $block['attrs']['url'] ) ? esc_url( $block['attrs']['url'] ) : '#';
                    $text = isset( $block['innerHTML'] ) ? wp_kses_post( $block['innerHTML'] ) : 'Click Me';
                    
                    // Custom render
                    $block_content = '<a href="' . $url . '" class="custom-btn">' . $text . '</a>';
                }
            
                return $block_content;
            }, 10, 2 );
        }
    }

    fea_instance()->gutenberg = new Frontend_Admin_Gutenberg();

endif;    