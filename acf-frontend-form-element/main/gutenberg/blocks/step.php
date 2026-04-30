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
            $active_step = 1;

            global $step_index, $fea_steps_context;

            $steps_total = $fea_steps_context['total_steps'] ?? 0;

            //get parent block to determine the current step index
            $parent = $block->parent_client_id;

     

            $step_index = isset($step_index) ? $step_index + 1 : 1;

            $current_step = $step_index ?? 0;
            $next_button = $attr['next_button_text'] ?? __('Next', 'frontend-admin');
            $prev_button = $attr['prev_button_text'] ?? __('Previous', 'frontend-admin');
         
            

            $render = sprintf(
                '<div 
                    class="fea-step %s"
                    data-step="%d"
                >%s',
                $current_step == $active_step ? '' : 'frontend-admin-hidden',
                $step_index,
                $content
            );
     

            //add next and previous buttons here if needed
            $next_button = sprintf(
                '<button
                    data-wp-on--click="actions.setStep"
                    type="button" 
                    data-step="%s"
                    data-button="next"
                    class="fe-step-next change-step">%s
                </button>',
                $steps_total == $current_step ? 'submit' : $current_step + 1,
                $next_button
            );
            $prev_button = sprintf(
                '<button
                    data-wp-on--click="actions.setStep"
                    type="button" 
                    data-step="%d"
                    data-button="prev"      
                    class="change-step">%s
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