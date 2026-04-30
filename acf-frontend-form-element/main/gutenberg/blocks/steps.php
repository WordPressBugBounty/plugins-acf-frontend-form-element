<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Form_Steps') ) :

    class Form_Steps
    {    
         function __construct()
        {
            add_filter('pre_render_block', [ $this, 'pre_render' ], 10, 3);
        }

        function pre_render($block_content, $block) {
            if ( $block['blockName'] === 'frontend-admin/steps' ) {
                global $step_index, $fea_steps_context;
                $step_index = 0;
                $fea_steps_context = [
                    'active_step' => 1,
                    'tab_links' => false,
                    'tab_align' => 'left',
                    'validate_steps' => false,
                    'total_steps' => count( $block['innerBlocks'] )
                ];
            }
            return $block_content;
        }
    

        public function render($attr, $content, $block)
        {
            if ( empty( $block->inner_blocks ) ) {
                return '<p>' . esc_html__( 'Please add some steps to this block.', 'frontend-admin' ) . '</p>';
            }

            $active_step = 0;
            $tab_links = isset( $attr['tab_links'] ) ? boolval( $attr['tab_links'] ) : false;
            $tab_align = isset( $attr['tabs_align'] ) ? $attr['tabs_align'] : 'left';
            $validate_steps = isset( $attr['validate_steps'] ) ? boolval( $attr['validate_steps'] ) : false;

            global $fea_steps_context;
            $steps_total = count( $block->inner_blocks );


            $fea_steps_context = [
                'active_step' => $active_step,
                'tab_links' => $tab_links,
                'tab_align' => $tab_align,
                'validate_steps' => $validate_steps,
                'total_steps' => $steps_total,
            ];

            $tabs  = '';
            $steps = '';

            if( ! empty( $attr['steps_counter_display'] ) ){
                $the_step = '<span class="current-step">' . $active_step . '</span>';

					if ( isset( $attr['counter_text'] ) ) {
						$counter_text = str_replace( '{current_step}', $the_step, $attr['counter_text'] );
						$counter_text = str_replace( '{total_steps}', $steps_total, $counter_text );
					} else {
						  $counter_text = $attr['counter_prefix'] . $the_step . $attr['counter_suffix'];
					}

                $counter = sprintf(
                    '<div class="fe-form-steps-counter">%s</div>',
                    $counter_text
                );
            }

            if ( ! empty( $attr['steps_tabs_display'] ) ) {

                foreach ( $block->inner_blocks as $index => $step_block ) {

                    $title = ! empty( $step_block->attributes['title'] )
                        ? $step_block->attributes['title']
                        : 'Step ' . ( $index + 1 );

                    $active_class = $index === $active_step ? 'active' : '';

                   
                    $tabs .= sprintf(
                        '<button 
                            type="button"                                             
                            class="form-tab %s %s"
                            data-step="%d"                          
                        >%s</button>',
                        $active_class,
                        $tab_links ? 'change-step' : '',
                        $index + 1,
                        esc_html( $title )
                    );

                }

            }

          

            $container_attributes = sprintf(
                'class="frontend-admin-steps fea-form-steps-tabs-align-%s"',
                esc_attr( $tab_align )
            );

            if( $validate_steps ){
                $container_attributes .= ' data-validate-steps="true"';
            }



            ob_start();
            ?>

            <div
                <?php echo $container_attributes; ?>
            >
                
                <?php if( ! empty( $counter ) ){ echo $counter; } ?>

                <?php if ( ! empty( $tabs ) ) : ?>
                    <div class="fe-form-steps-tabs">
                        <?php echo $tabs; ?>
                    </div>
                <?php endif; ?>

                 <div class="fe-form-steps-content">
                    <?php echo $content; ?>
                </div>


            </div>

            <style>
                .fe-form-steps {
                    border: 1px solid #ddd;
                    padding: 20px;
                    }

                    .fe-form-steps-tabs {
                    display: flex;
                    gap: 8px;
                    margin-bottom: 20px;
                    }

                    .fe-step-tab {
                    padding: 8px 14px;
                    border: 1px solid #ccc;
                    background: white;
                    cursor: pointer;
                    }

                    .fe-step-tab.is-active {
                    background: #007cba;
                    color: white;
                    border-color: #007cba;
                    }

                  
            </style>
            <?php
           

            return ob_get_clean();
        }
      
    }


endif;    