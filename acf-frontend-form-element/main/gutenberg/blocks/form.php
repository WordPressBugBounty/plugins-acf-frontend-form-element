<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Form') ) :

    class Form
    {

        public function render($attr, $content){
	
            $_settings = apply_filters( 'frontend_admin/show_form', $attr, 'form' );

            if( ! $_settings ) return;
            
            ob_start();
            global $fea_form, $fea_instance, $fea_scripts;

            $GLOBALS['admin_form'] = $fea_form;
         
            do_action( 'frontend_admin/gutenberg/before_render', $fea_form );

            $fea_instance->frontend->enqueue_scripts( 'frontend_admin_form' );
            $fea_scripts = true;

            

            echo '<form '. feadmin_get_esc_attrs( $fea_form['form_attributes'] ) .'>';
            if( $fea_form ) $fea_instance->form_display->form_render_data( $fea_form );

            echo $content;
            echo '</form>';

            do_action( 'frontend_admin/gutenberg/after_render', $fea_form );
            return ob_get_clean();

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

            $post_types = get_post_types([],'objects');

            $post_types_options = [];
            if( $post_types ){
                foreach ( $post_types as $post_type ) {
                    $post_types_options[] = [
                        'value' => $post_type->name,
                        'label' => $post_type->labels->singular_name,
                    ];
                 }
            }

            $localization_data = [
                'restUrl'  => rest_url( 'fea/v2' ),
                'nonce'    => wp_create_nonce('wp_rest'),
                'postTypes' => $post_types_options,
            ];
        
            wp_localize_script(
                'frontend-admin-form-editor-script',
                'feaData',
                $localization_data
            );
        }

        function block_render( $block_content, $block ) {  
            global $fea_form;
            
            if( 'frontend-admin/form' == $block['blockName'] ){
                $fea_form = null;                
            }

            return $block_content;
        }

        function form_inner_block_render( $block_content, $block ) {  
            global $fea_instance, $fea_form, $post;

            if( $fea_form ){
                $post_id = $fea_form['post_id'] ?? 'none';

                if( 'none' == $post_id && $fea_form['hide_if_no_post'] ){
                    return false;
                }
            }
            return $block_content;
        }
        function pre_block_render( $block_content, $block ) {  
            global $fea_instance, $fea_form, $wp_query;
            $current_post_id = $wp_query->get_queried_object_id();

           
            if( 'frontend-admin/form' == $block['blockName'] ){
                $form_display = $fea_instance->form_display;
                    if( ! $fea_form ){

                        $attrs = $block['attrs'];
                    
                        $form_data = $attrs['form_settings'];

                        $post_to_edit = $form_data['post_to_edit'] ?? 'current_post';

                        if( 'new_post' == $post_to_edit ){
                            $form_data['save_to_post'] = 'new_post';
                        }else{
                            $form_data['save_to_post'] = 'edit_post';
                        }

                        if( $attrs['form_key'] ){
                            $form_data['id'] = $current_post_id . '_gutenberg_' . $attrs['form_key'];
                            $form_data['ID'] = $current_post_id . '_gutenberg_' . $attrs['form_key'];
                        }
                        $fea_form =  $form_display->validate_form( $form_data );
                        
                    }
            }

            return $block_content;
        }

        function get_form_variation( $args ){
            $form_title = sprintf( __( '%s %s Form', 'frontend-admin' ), $args['save_type_label'], $args['post_type_label'] );
            $inner_blocks = [
                ['core/heading', [ 'content' => $form_title ] ],
                ['frontend-admin/post-title-field', [ 'label' => sprintf( __( '%s Title', 'frontend-admin' ), $args['post_type_label']) ] ],
                ['frontend-admin/post-excerpt-field', [ 'label' => sprintf( __( '%s Excerpt', 'frontend-admin' ), $args['post_type_label']) ] ],
                
            ];
            
            if ( function_exists('acf_get_field_groups') ) {
                $field_groups = acf_get_field_groups( [ 'post_type' => $args['post_type'] ] );
                if ( ! empty( $field_groups ) ) {
                    foreach ( $field_groups as $field_group ) {
                        $inner_blocks[] = ['core/heading', [ 'content' => $field_group['title'], 'fontSize' => 'medium' ] ];
                        $inner_blocks[] = [
                            'frontend-admin/fields-select-field', 
                            [ 'fields_select' => [$field_group['key'] ]], 
                        ];
                    }
                }
            }

            $inner_blocks[] = [
                'core/buttons',
                ['className' => 'fea-form-buttons'],
                [
                    ['core/button', [
                        'className' => 'fea-submit-button',
                        'isPrimary' => true,
                        'text' => __( 'Submit', 'frontend-admin' ),
                        'submitButton' => true,
                    ]],
                ]
            ];

            return array(
                'name'        => $args['save_type'] . '_' . $args['post_type'],
                'title'       => $form_title,
                'description' => '',
                'isDefault'   => false,
                'innerBlocks' => $inner_blocks,
                'attributes' => $args['attributes']
            );
        }
   
        function block_variations( $variations, $block_type ) {
            if ( 'frontend-admin/form' !== $block_type->name ) {
                return $variations;
            }

            $post_types = get_post_types( [ 'public' => true, 'publicly_queryable' => true, 'exclude_from_search' => false ], 'objects' );

            foreach( $post_types as $post_type ){
                // Add a custom variation
                $variations[] = $this->get_form_variation( [
                    'save_type' => 'new', 
                    'save_type_label' => 'New',
                    'post_type' => $post_type->name, 
                    'post_type_label' => $post_type->labels->singular_name,
                    'attributes' => [
                        'form_settings' => [
                            'post_to_edit' => 'new_post',
                            'new_post_type' => $post_type->name,
                        ]
                    ]
               ] );
               $variations[] = $this->get_form_variation( [
                    'save_type' => 'edit', 
                    'save_type_label' => 'Edit',
                    'post_type' => $post_type->name, 
                    'post_type_label' => $post_type->labels->singular_name,
                    'attributes' => [
                        'form_settings' => [
                            'post_to_edit' => 'current_post',
                            'post_type' => [ $post_type->name ],
                        ]
                    ]
                ] );
               
            }
        
            
        
            return $variations;
        }


      
        public function get_form_block( $form, $key, $element = false ){
         
            if ( ! is_string( $key ) && strpos( $key, '_gutenberg_' ) === false ) {
                return $form;
            }
            // Get Template/page id and block id
            $ids = explode( '_gutenberg_', $key );

            // If there is no block id, there is no reason to continue 
            if( empty( $ids[1] ) ) return $form; 
            

            global $fea_instance, $fea_form, $post;
            $block = $fea_instance->gutenberg->get_the_block( $ids );


            if( $block ){		
                $form_display = $fea_instance->form_display;
    
                $form_data = $block['attrs']['form_settings'];
                $form_data['id'] = $key;
                $form_data['ID'] = $key;
                $post_to_edit = $form_data['post_to_edit'] ?? 'current_post';

                if( 'new_post' == $post_to_edit ){
                    $form_data['save_to_post'] = 'new_post';
                }else{
                    $form_data['save_to_post'] = 'edit_post';
                }
                $fea_form =  $form_display->validate_form( $form_data );


                $fea_form['fields'] = $this->get_form_fields( $block, $fea_form );


                return $fea_form;
                /* $form = $block->prepare_form();

                if( $element ){
                    $form['object'] = $block;
                }
                return $form; */
            }
            return false;
        }

        public function get_fields_from_select( $fields_select, $fields_exclude, $form ){
            $fields = [];
            if( empty( $fields_select ) ) return $fields;
            
            foreach( $fields_select as $field_key ){
                if( strpos( $field_key, 'group_' ) === 0 ){
                    // if the field key is a group, get the fields from the group
                    $field_group = acf_get_field_group( $field_key );
                    if( ! $field_group ) continue;
                    $group_fields = acf_get_fields( $field_group );
                    if( ! $group_fields ) continue;
                    foreach( $group_fields as $group_field ){
                        //if the field is in fields exclude, skip it
                        if( in_array( $group_field['key'], $fields_exclude ) || in_array( $group_field['name'], $fields_exclude ) ){
                            continue;
                        }
                        $fields[$group_field['key']] = $group_field;
                        $fields[$group_field['key']]['builder'] = 'gutenberg';
                    }
                }else{
                    // if the field key is a field, get the field
                    $field = acf_get_field( $field_key );
                    if( ! $field ) continue;
                    $field['builder'] = 'gutenberg';
                    $fields[$field['key']] = $field;
                }
            }

            return $fields;
        }

        public function get_form_fields( $block, $form ){
            $fields = [];
            if( ! empty( $block['innerBlocks'] ) ){
                foreach( $block['innerBlocks'] as $inner_block ){
                    if( empty( $inner_block['attrs']['field_key'] ) ){
                        // if the inner block does not have a field_key, it is not a field block. But if it is a frontend-admin/fields-select-field, then get the fields from the selected field groups or the keys
                        if( 'frontend-admin/fields-select-field' == $inner_block['blockName'] ){
                            if( ! empty( $inner_block['attrs']['fields_select'] ) ){
                                $select_attrs = $inner_block['attrs'];
                                if( ! isset( $select_attrs['fields_exclude'] ) ){
                                    $select_attrs['fields_exclude'] = [];
                                }
                                $fields = array_merge( $fields, $this->get_fields_from_select( $select_attrs['fields_select'], $select_attrs['fields_exclude'], $form ) );
                            }
                            continue;
                        }
                        if( empty( $inner_block['innerBlock'] ) ){
                            continue;
                        }
                        $fields = array_merge( $fields, $this->get_form_fields( $inner_block, $form ) );
                    }

                    $field_type = str_replace(
                        array( 'frontend-admin/', '-field' ),
                        array( '', '' ),
                        $inner_block['blockName']
                    );

                    //get field type attributes from the block json located at assets/build/blocks/{$field_type}/block.json
                    $field_json_path = FEA_DIR . 'assets/build/blocks/' . $field_type . '/block.json';
                    if( ! file_exists( $field_json_path ) ){
                        error_log( 'Field type block json not found: ' . $field_json_path );
                        continue;
                    }

                    $field_json = json_decode( file_get_contents( $field_json_path ), true );
                    if( ! $field_json || ! isset( $field_json['attributes'] ) ){
                        error_log( 'Field type block json is invalid: ' . $field_json_path );
                        continue;
                    }

                    $field_type_attributes = $field_json['attributes'] ?? [];

                    foreach( $field_type_attributes as $attr_key => $attr_value ){
                        if( ! isset( $inner_block['attrs'][$attr_key] ) ){
                            $inner_block['attrs'][$attr_key] = $attr_value['default'] ?? '';
                        }
                    }
                    // Get the field attributes and set the field type


                    $field = acf_get_valid_field($inner_block['attrs']);

                    $field['type'] = str_replace( '-', '_', $field_type );



                    $field_key = $field['field_key'] ?? uniqid();
                    $field['key'] = $form['id'] . '_' .$field_key;
                    $field['builder'] = 'gutenberg';
                    
                    $field['name'] = $inner_block['attrs']['name'] ?? 'fea_' . $field['type'];
                    $fields[$field['key']] = $field;
                }
            }
            return $fields;
        }

        public function get_field_block( $field,  $key ){
			if ( $field && strpos( $key, '_gutenberg_' ) === false ) {
				return $field;
			}
	
			// Get Template/page id and block id
			$ids = explode( '_gutenberg_', $key );


			// If there is no block id, there is no reason to continue 
			if( empty( $ids[1] ) ) return $field; 		

			$post_id = $ids[0];

			global $fea_current_post_id, $fea_instance, $fea_form;
			$fea_current_post_id = $post_id;	

            if( ! empty( $fea_form['fields'][$key] ) ){
                return $fea_form['fields'][$key];
            }
			

            $block = $fea_instance->gutenberg->get_the_block( $ids );			

			if( $block ){	
                $field = acf_get_valid_field($block['attrs']);

                $field_key = $field['field_key'] ?? uniqid();
                $field['key'] = $post_id . '_gutenberg_' . $field_key;
                $field['builder'] = 'gutenberg';
            
                $field['type'] = str_replace(
                    array( 'frontend-admin/', '-field', '-' ),
                    array( '', '', '_' ),
                    $block['blockName']
                );

                $field['name'] = $block['attrs']['name'] ?? 'fea_' . $field['type'];

                return $field;
                /* 

				if( empty( $field_id ) ) return $block->prepare_field( $key );

				$form = $block->prepare_form( $key );
								error_log( print_r( $form, true ) );

			
				if( ! empty( $form['fields'][$key] ) ) return $form['fields'][$key]; */
			}
			return $field;
	
		}


        public function __construct()
        {
          

            add_filter( 'frontend_admin/forms/get_form', [ $this, 'get_form_block' ], 10, 3 );
			add_filter( 'frontend_admin/fields/get_field', [ $this, 'get_field_block' ], 10, 2 );


            add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );

            add_filter( 'pre_render_block', [ $this, 'pre_block_render' ], 10, 2 );
            add_filter( 'render_block', [ $this, 'block_render' ], 10, 2 );
            add_filter( 'render_block', [ $this, 'form_inner_block_render' ], 12, 2 );

            add_filter( 'get_block_type_variations', [ $this, 'block_variations' ], 10, 2 );

        }
    }


endif;    