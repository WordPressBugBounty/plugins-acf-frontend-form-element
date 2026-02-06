<?php
namespace Frontend_Admin;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin_Gutenberg') ) :

    class Gutenberg
    {

        public function register_blocks()
        {
            $blocks = [ 
                'form' => 'Form',
                'admin-form' => 'Form_Select',
                'submissions' => 'Submissions_Select'
               // 'field' => 'field'
            ];

            foreach( $blocks as $block => $className ){
                include_once( FEA_DIR . 'main/gutenberg/blocks/' . $block . '.php' );
                $className = "\Frontend_Admin\Gutenberg\\".$className;
                $class = new $className;
                $name = str_replace( '_', '-', $block );
                register_block_type(
                    FEA_DIR . "/assets/build/blocks/$name", [
                    'render_callback' => [ $class, 'render' ],
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
        
            global $fea_instance, $fea_form, $post;
            $form_display = $fea_instance->form_display;
            
            $render = '';        
            $field = acf_get_valid_field($attr);

        
            $field_key = $field['field_key'] ?? uniqid();
            $field['key'] = !empty( $fea_form['id'] ) ? $fea_form['id'] . '_' . $field_key : $field_key;
            $field['builder'] = 'gutenberg';
           
            $field['type'] = str_replace(
                array( 'frontend-admin/', '-field', '-' ),
                array( '', '', '_' ),
                $block->name
            );
            do_action( 'frontend_admin/form_assets/type=' . $field['type'], $field );

            $field['name'] = $attr['name'] ?? 'fea_' . $field['type'];

            $field = $form_display->get_field_data_type( $field, $fea_form );

            if( ! $field ) return false;


            if ( ! isset( $field['value'] )
                || $field['value'] === null
            ) {
                $field = $form_display->get_field_value( $field, $fea_form );
                
            }

                
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


            ob_start();
            
            
                fea_instance()->form_display->render_field_wrap( $field );
            
            $render = ob_get_contents();
            ob_end_clean();    
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
                'fea-block-settings',
                FEA_URL . '/assets/build/block-settings/index.js',
                ['wp-edit-post'],
                '1.0',
                true
            );

            do_action( 'frontend_admin/enqueue_block_editor_assets' );
        }

        function block_visibilty( $block_content, $block ) {
            // Fetch block attributes
            $attrs = $block['attrs'] ?? [];

            // If visibility isn't limited → always show
            if ( empty( $attrs['limitVisibility'] ) ) {
                return $block_content;
            }

            $rules_groups = $attrs['visibilityRules'] ?? [];

            // If there are no rules, hide nothing
            if ( empty( $rules_groups ) ) {
                return $block_content;
            }


            // Helper: check one rule
            $evaluate_rule = function( $rule ) {
                global $post;

                $user = wp_get_current_user();
                $is_logged_in = is_user_logged_in();

                $showFor      = $rule['showFor'] ?? 'everyone';
                $roles        = $rule['showForRoles'] ?? [];
                $message      = $rule['noPermissionsMessage'] ?? '';
                $isPostAuthor = $rule['isPostAuthor '] ?? false;

                $field        = $rule['field'] ?? '';
                $operator     = $rule['operator'] ?? 'equals';
                $value        = $rule['value'] ?? '';

                // --- SHOW FOR LOGIC ---------------------------------------

                // Show for logged in users only
                if ( $showFor === 'logged_in' ) {
                    if ( ! $is_logged_in ) {
                        return [ false, $message ];
                    }

                    // Role restriction
                    if ( ! empty( $roles ) ) {
                        $user_roles = (array) $user->roles;
                        $intersect = array_intersect( $user_roles, $roles );

                        if ( empty( $intersect ) ) {
                            return [ false, $message ];
                        }
                    }




                    if( $isPostAuthor ){
                        // If rule requires the current user to be the post author, check it.
                        if ( empty( $post ) || ! isset( $post->post_author ) || (int) $post->post_author !== (int) $user->ID ) {
                            return [ false, $message ];
                        }
                    }

                }

                // Show for logged out users only
                if ( $showFor === 'logged_out' ) {
                    if ( $is_logged_in ) {
                        return [ false, $message ];
                    }
                }

                               

                // Optional: If no field is set, rule passes
                if ( empty( $field ) ) {
                    return [ true, '' ];
                }

                // Fetch field value
                $field_val = get_post_meta( $post->ID, $field, true ); 

                // ^ You must define this helper for your plugin.

                switch ( $operator ) {
                    case 'equals':
                        $pass = $field_val == $value;
                        break;
                    case 'not_equals':
                        $pass = $field_val != $value;
                        break;
                    case 'contains':
                        $pass = is_string( $field_val ) && strpos( $field_val, $value ) !== false;
                        break;
                    case 'greater_than':
                        $pass = floatval( $field_val ) > floatval( $value );
                        break;
                    case 'less_than':
                        $pass = floatval( $field_val ) < floatval( $value );
                        break;
                    default:
                        $pass = true;
                }

                return [ $pass, $message ];
            };

            // --- OR GROUPS LOGIC -------------------------------------------------

            $block_visible = false;
            $message_to_show = '';

            foreach ( $rules_groups as $and_group ) {

                $and_pass = true;
                $group_message = '';

                foreach ( $and_group as $rule ) {

                    list( $pass, $msg ) = $evaluate_rule( $rule );

                    if ( ! $pass ) {
                        $and_pass = false;
                        $group_message = $msg; // Preserve message for failed rule
                        break;
                    }
                }

                // One group passing is enough to show the block
                if ( $and_pass ) {
                    $block_visible = true;
                    break;
                }

                // If this group failed and no group passed yet, store message
                if ( ! $block_visible && ! empty( $group_message ) ) {
                    $message_to_show = $group_message;
                }
            }

            // --- FINAL OUTPUT ----------------------------------------------------

            if ( $block_visible ) {
                return $block_content;
            }

            // If hidden:
            return ! empty( $message_to_show ) ? wp_kses_post( $message_to_show ) : '';
        }

        function block_render( $block_content, $block ) {
            global $fea_form;



            // Get block attributes safely
            $attrs = $block['attrs'] ?? [];


            if ( 'core/button' === $block['blockName'] ) {
                $submit = $attrs['submitButton'] ?? false;

                if ( $submit ) {
                    if ( ! $fea_form ) {
                        return $block_content;
                    } else {
                        if ( ! empty( $block_content ) ) {
                            $block_content = preg_replace(
                                '/<a\s+([^>]*?)class="([^"]*)"/is',
                                '<a $1class="fea-submit-button $2"',
                                $block_content
                            );
                        }
                        return $block_content;
                    }
                }
            }

            return $block_content;
        }

       

        public function get_the_block( $ids, $type = 'form' ) {
            $post_id = $ids[0];
            $key = $ids[1];

            $post_content = get_post_field( 'post_content', $post_id );

            if ( empty( $post_content ) ) {
                $template = get_block_template( $post_id );
                if ( $template ) {
                    $post_content = $template->content;
                }else{
                    return null;
                }
            }

            if( 'field' == $type ){
                $keys = explode( '_', $key );
                $key = end( $keys );
            }

            $blocks = parse_blocks( $post_content );

            foreach ( $blocks as $block ) {
                $block = $this->recursive_block_search( $block, $key, $type );
                if ( $block ) {
                    return $block;
                }
            }

            return null;
        }

        public function recursive_block_search( $block, $key, $type = 'form' ) {
            if ( ! is_array( $block ) ) {
                return null;
            }

            if ( ! empty( $block['attrs'][$type.'_key'] ) && $block['attrs'][$type.'_key'] === $key ) {
                return $block;
            }

            if ( ! empty( $block['innerBlocks'] ) ) {

                foreach ( $block['innerBlocks'] as $inner_block ) {
                    $result = $this->recursive_block_search( $inner_block, $key, $type );
                    if ( $result ) {
                        return $result;
                    }
                }
            }

            return null;
        }

      
        public function rest_pre_dispatch( $result, $server, $request ) {

            if ( strpos( $request->get_route(), '/wp/v2/block-renderer' ) !== false ) {
                $attributes_to_remove = [ 'limitVisibility', 'visibilityRules' ];

                $attributes = $request['attributes'] ?? []; 
                foreach ( $attributes_to_remove as $attr ) {
                    if ( isset( $attributes[ $attr ] ) ) {
                        unset( $attributes[ $attr ] );
                    }
                }
                $request['attributes'] = $attributes;

             
            }

            return $result;
        }



        public function __construct()
        {
            add_filter('block_categories_all', array( $this, 'add_block_categories' ));
            add_action('init', array( $this, 'register_blocks' ), 20);

            add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

            add_filter( 'render_block', [ $this, 'block_render' ], 10, 2 );

            add_filter( 'render_block', [ $this, 'block_visibilty' ], 9, 2 );

		    add_filter( 'rest_pre_dispatch', array( $this, 'rest_pre_dispatch' ), 10, 3 );

        }
    }

    fea_instance()->gutenberg = new Gutenberg();

endif;    