<?php
namespace Frontend_Admin\Walkers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Related_Terms_Walker' ) ) :

	class Related_Terms extends \Walker {


		/**
		 * What the class handles.
		 *
		 * @since 2.1.0
		 * @var   string
		 */
		public $tree_type = 'category';

		/**
		 * DB fields to use.
		 *
		 * @since 2.1.0
		 * @var   array
		 */
		public $db_fields = array(
			'parent' => 'parent',
			'id'     => 'term_id',
		);

		/**
		 * The field being rendered.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public $field;

		/**
		 * Constructor
		 *
		 * @date  20/4/21
		 * @since 1.0.0
		 *
		 * @param  array $field The field being rendered.
		 * @return void
		 */
		function __construct( $field ) {
			$this->field = $field;
		}

		/**
		 * Starts the list before the elements are added.
		 *
		 * @see Walker:start_lvl()
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param int    $depth  Depth of category. Used for tab indentation.
		 * @param array  $args   An array of arguments. @see wp_terms_checklist()
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent<ul class='children acf-bl'>\n";
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @see Walker::end_lvl()
		 *
		 * @since 1.0.0
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param int    $depth  Depth of category. Used for tab indentation.
		 * @param array  $args   An array of arguments. @see wp_terms_checklist()
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}

		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 *
		 * @since 1.0.0
		 *
		 * @param string  $output Used to append additional content (passed by reference).
		 * @param WP_Term $term   The current term object.
		 * @param int     $depth  Depth of the term in reference to parents. Default 0.
		 * @param array   $args   An array of arguments. @see wp_terms_checklist()
		 * @param int     $id     ID of the current term.
		 */
		public function start_el( &$output, $term, $depth = 0, $args = array(), $id = 0 ) {
			 $is_selected = in_array( $term->term_id, $this->field['value'] );

			// Generate array of checkbox input attributes.
			$input_attrs = array(
				'type'  => $this->field['field_type'],
				'name'  => $this->field['name'],
				'value' => $term->term_id,
			);
			if ( $is_selected ) {
				$input_attrs['checked'] = true;
			}

			if ( ! empty( $this->field['show_as_buttons'] ) ) {
				$input_attrs['hidden'] = true;
				$display = '<button type="button" class="acf-button tax-btn" data-id="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</button>';
			}else{
				$display = '<span>' . acf_esc_html( $term->name ) . '</span>';
			}


				$output .= "\n" . '<li class="related-term" data-id="' . esc_attr( $term->term_id ) . '">' .
				'<label' . ( $is_selected ? ' class="selected"' : '' ) . '>' .
				'<input ' . acf_esc_attrs( $input_attrs ) . '/> ' .
				$display .
				'</label>';
		

			/*
			 if( $this->field['add_term'] ){
			$output .= '<a href="#" class="acf-icon -pencil small render-form" data-name="edit_item"></a>';
			} */
		}

		/**
		 * Ends the element output, if needed.
		 *
		 * @see Walker::end_el()
		 *
		 * @since 1.0.0
		 *
		 * @param string  $output   Used to append additional content (passed by reference).
		 * @param WP_Term $category The current term object.
		 * @param int     $depth    Depth of the term in reference to parents. Default 0.
		 * @param array   $args     An array of arguments. @see wp_terms_checklist()
		 */
		public function end_el( &$output, $category, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
		}
	}

endif;
