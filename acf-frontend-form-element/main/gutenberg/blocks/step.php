<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Form_Step') ) :

    class Form_Step
    {    
    

        public function render($attr, $content, $block)
        {
            $active_step = $block->context['frontend-admin/steps/active_step'] ?? 0;

            global $step_index, $steps_context;

          
            $step_index = isset($step_index) ? $step_index + 1 : 1;

            $current_step = $step_index ?? 0;
            $next_button = $attr['next_button_text'] ?? __('Next', 'frontend-admin');
            $prev_button = $attr['prev_button_text'] ?? __('Previous', 'frontend-admin');
         

            $render = sprintf(
                '<div 
                    class="fe-form-step"
                    data-wp-context=\'{"step":%d}\'
                    data-wp-bind--hidden="callbacks.stepActive"
                >%s',
                $step_index,
                $content
            );

            //add next and previous buttons here if needed
            $next_button = sprintf(
                '<button
                    data-wp-on--click="actions.setStep"
                    data-wp-context=\'{"step":%d}\'
                    type="button" 
                    class="fe-step-next">%s
                </button>',
                $current_step + 1,
                $next_button
            );
            $prev_button = sprintf(
                '<button
                    data-wp-on--click="actions.setStep"
                    data-wp-context=\'{"step":%d}\'
                    type="button" 
                    class="fe-step-prev">%s
                </button>',
                $current_step - 1,
                $prev_button
            );



            $render .= '<div class="fe-step-navigation">';
            if ( $current_step > 1 ) {
                $render .= $prev_button;
            }
            $render .= $next_button;

            $render .= '</div>';

            $render .= '</div>';

            return $render;
        }
      
    }


endif;    