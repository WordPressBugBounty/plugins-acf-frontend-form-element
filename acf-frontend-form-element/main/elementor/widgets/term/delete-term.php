<?php
namespace Frontend_Admin\Elementor\Widgets;

use Frontend_Admin\Plugin;

use Frontend_Admin\Classes;
use Elementor\Controls_Manager;

/**

 *
 * @since 1.0.0
 */
class Delete_Term_Widget extends Delete_Post_Widget {


	/**
	 * Get widget name.
	 *
	 * Retrieve acf ele form widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'delete_term';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve acf ele form widget title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Delete Term', 'frontend-admin' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve acf ele form widget icon.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-trash frontend-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the acf ele form widget belongs to.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'frontend-admin-taxonomies' );
	}

	/**
	 * Register acf ele form widget controls.
	 *
	 * Adds different input fields to allow the term to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'delete_button_section',
			array(
				'label' => __( 'Trash Button', 'frontend-admin' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'delete_button_text',
			array(
				'label'       => __( 'Delete Button Text', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Delete', 'frontend-admin' ),
				'placeholder' => __( 'Delete', 'frontend-admin' ),
			)
		);
		$this->add_control(
			'delete_button_icon',
			array(
				'label' => __( 'Delete Button Icon', 'frontend-admin' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'confirm_delete_message',
			array(
				'label'       => __( 'Confirm Delete Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'The term will be deleted. Are you sure?', 'frontend-admin' ),
				'placeholder' => __( 'The term will be deleted. Are you sure?', 'frontend-admin' ),
			)
		);

		$this->add_control(
			'show_delete_message',
			array(
				'label'        => __( 'Show Success Message', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'default'      => 'true',
				'return_value' => 'true',
			)
		);
		$this->add_control(
			'delete_message',
			array(
				'label'       => __( 'Success Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'You have deleted this term', 'frontend-admin' ),
				'placeholder' => __( 'You have deleted this term', 'frontend-admin' ),
				'dynamic'     => array(
					'active'    => true,
					'condition' => array(
						'show_delete_message' => 'true',
					),
				),
			)
		);

		$this->add_control(
			'delete_redirect',
			array(
				'label'   => __( 'Redirect After Delete', 'frontend-admin' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom_url',
				'options' => array(
					'current'     => __( 'Reload Current Url', 'frontend-admin' ),
					'custom_url'  => __( 'Custom Url', 'frontend-admin' ),
					'referer_url' => __( 'Referer', 'frontend-admin' ),
				),
			)
		);

		$this->add_control(
			'redirect_after_delete',
			array(
				'label'         => __( 'Custom URL', 'frontend-admin' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'Enter Url Here', 'frontend-admin' ),
				'show_external' => false,
				'dynamic'       => array(
					'active' => true,
				),
				'condition'     => array(
					'delete_redirect' => 'custom_url',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'term_section',
			array(
				'label' => __( 'Term', 'frontend-admin' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		fea_instance()->local_actions['term']->action_controls( $this, false, 'delete_term' );

		$this->end_controls_section();

		do_action( 'frontend_admin/elementor/permissions_controls', $this );

		if ( empty( fea_instance()->pro_features ) ) {

			$this->start_controls_section(
				'style_promo_section',
				array(
					'label' => __( 'Styles', 'frontend-admin' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'styles_promo',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( '<p><a target="_blank" href="https://www.dynamiapps.com/"><b>Go Pro</b></a> to unlock styles.</p>', 'frontend-admin' ),
					'content_classes' => 'acf-fields-note',
				)
			);

			$this->end_controls_section();

		} else {
			do_action( 'frontend_admin/delete_button_styles', $this );
		}

	}

	
	public function prepare_field(){
		$local_field = parent::prepare_field();

		if( ! $local_field ) {
			return false;
		}

		$local_field['type'] = 'delete_term';
		return $local_field;
	}

	
}
