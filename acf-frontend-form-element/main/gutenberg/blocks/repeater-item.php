<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Repeater_Item') ) :

    class Repeater_Item
    {    
    

        public function render($attr, $content, $block)
        {
           return sprintf(
		'<div class="fe-repeater-item">%s</div>',
		$content
	);

        }
      
    }


endif;    