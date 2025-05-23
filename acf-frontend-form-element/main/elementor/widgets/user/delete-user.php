<?php
namespace Frontend_Admin\Elementor\Widgets;

use Frontend_Admin\Plugin;

use Frontend_Admin\Classes;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

/**

 *
 * @since 1.0.0
 */
class Delete_User_Widget extends Delete_Post_Widget {


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
		return 'delete_user';
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
		return __( 'Delete User', 'acf-frontend-form-element' );
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
		return array( 'frontend-admin-users' );
	}
	/**
	 * Register acf ele form widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'delete_button_section',
			array(
				'label' => __( 'Trash Button', 'acf-frontend-form-element' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'delete_button_text',
			array(
				'label'       => __( 'Delete Button Text', 'acf-frontend-form-element' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Delete', 'acf-frontend-form-element' ),
				'placeholder' => __( 'Delete', 'acf-frontend-form-element' ),
			)
		);
		$this->add_control(
			'delete_button_icon',
			array(
				'label' => __( 'Delete Button Icon', 'acf-frontend-form-element' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'actions_section',
			array(
				'label' => __( 'Actions', 'acf-frontend-form-element' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		if ( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ) {
			$this->add_control(
				'reassign_posts',
				array(
					'label'       => __( 'Reassign Posts To...', 'acf-frontend-form-element' ),
					'description' => __( 'Enter user ID. If left empty, all of the user\'s posts will be deleted.', 'acf-frontend-form-element' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'acf-frontend-form-element' ),
				)
			);
		} else {
			$this->add_control(
				'reassign_posts',
				array(
					'label'        => __( 'Reassign Posts To...', 'acf-frontend-form-element' ),
					'description'  => __( 'If left empty, all of the user\'s posts will be deleted.', 'acf-frontend-form-element' ),
					'label_block'  => true,
					'type'         => Query_Module::QUERY_CONTROL_ID,
					'autocomplete' => array(
						'object'  => Query_Module::QUERY_OBJECT_USER,
						'display' => 'detailed',
					),
				)
			);
		}
		$this->add_control(
			'confirm_delete_message',
			array(
				'label'       => __( 'Confirm Delete Message', 'acf-frontend-form-element' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'The user will be deleted. Are you sure?', 'acf-frontend-form-element' ),
				'placeholder' => __( 'The user will be deleted. Are you sure?', 'acf-frontend-form-element' ),
			)
		);

		$this->add_control(
			'show_delete_message',
			array(
				'label'        => __( 'Show Success Message', 'acf-frontend-form-element' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'acf-frontend-form-element' ),
				'label_off'    => __( 'No', 'acf-frontend-form-element' ),
				'default'      => 'true',
				'return_value' => 'true',
			)
		);
		$this->add_control(
			'delete_message',
			array(
				'label'       => __( 'Success Message', 'acf-frontend-form-element' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'You have deleted this user', 'acf-frontend-form-element' ),
				'placeholder' => __( 'You have deleted this user', 'acf-frontend-form-element' ),
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
				'label'   => __( 'Redirect After Delete', 'acf-frontend-form-element' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom_url',
				'options' => array(
					'current'     => __( 'Reload Current Url', 'acf-frontend-form-element' ),
					'custom_url'  => __( 'Custom Url', 'acf-frontend-form-element' ),
					'referer_url' => __( 'Referer', 'acf-frontend-form-element' ),
				),
			)
		);

		$this->add_control(
			'redirect_after_delete',
			array(
				'label'         => __( 'Custom URL', 'acf-frontend-form-element' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'Enter Url Here', 'acf-frontend-form-element' ),
				'show_external' => false,
				'required'      => true,
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
			'user_section',
			array(
				'label' => __( 'User', 'acf-frontend-form-element' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		fea_instance()->local_actions['user']->action_controls( $this, false, 'delete_user' );

		$this->end_controls_section();

		do_action( 'frontend_admin/elementor/permissions_controls', $this );

		if ( empty( fea_instance()->pro_features ) ) {

			$this->start_controls_section(
				'style_promo_section',
				array(
					'label' => __( 'Styles', 'acf-frontend-form-element' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'styles_promo',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( '<p><a target="_blank" href="https://www.dynamiapps.com/"><b>Go Pro</b></a> to unlock styles.</p>', 'acf-frontend-form-element' ),
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
		$local_field['type'] = 'delete_user';
		$local_field['reassign_posts'] = $settings['reassign_posts'];
		return $local_field;
	}


}
