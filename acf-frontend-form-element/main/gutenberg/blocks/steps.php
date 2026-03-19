<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Form_Steps') ) :

    class Form_Steps
    {    
    

        public function render($attr, $content, $block)
        {
            $active_step = isset( $attr['active_step'] ) ? intval( $attr['active_step'] ) : 1;
            $tab_links = isset( $attr['tab_links'] ) ? boolval( $attr['tab_links'] ) : false;
            $tab_align = isset( $attr['tabs_align'] ) ? $attr['tabs_align'] : 'left';
            $validate_steps = isset( $attr['validate_steps'] ) ? boolval( $attr['validate_steps'] ) : false;

            global $steps_context;
            $steps_context = [
                'active_step' => $active_step,
                'tab_links' => $tab_links,
                'tab_align' => $tab_align,
                'validate_steps' => $validate_steps,
            ];

            $tabs  = '';
            $steps = '';

            if ( ! empty( $block->inner_blocks ) ) {

                foreach ( $block->inner_blocks as $index => $step_block ) {

                    $title = ! empty( $step_block->attributes['title'] )
                        ? $step_block->attributes['title']
                        : 'Step ' . ( $index + 1 );

                    $active_class = $index === $active_step ? 'active' : '';

                    $tab_attributes = $tab_links ? sprintf(
                        'data-wp-on--click="actions.setStep"',
                        $index + 1
                    ) : '';

                    $tabs .= sprintf(
                        '<button 
                            type="button"                                             
                            class="fe-step-tab"
                            data-wp-context=\'{"step":%d}\'
                            data-wp-class--is-active="callbacks.stepActive"
                            ' . $tab_attributes . '                            
                        >%s</button>',
                        $index + 1,
                        esc_html( $title )
                    );

                }

            }

          
            ob_start();
            ?>

            <div
                class="fe-form-steps"
                data-wp-interactive="frontend-admin/form"
                data-wp-context='<?php echo wp_json_encode( [
                    'activeStep' => 1,
                    'validateSteps' => $validate_steps,
                ] ); ?>'
            >

                <div class="fe-form-steps-tabs">
                    <?php echo $tabs; ?>
                </div>

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
            wp_enqueue_script_module(
                'fea-form-steps',
                plugins_url( 'assets/steps.js', __FILE__ ),
                [],
                '1.0.0',
            );

            return ob_get_clean();
        }
      
    }


endif;    