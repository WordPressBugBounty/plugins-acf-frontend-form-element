<?php

namespace Frontend_Admin\Elementor\Widgets;

use  Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly
}

/**

 *
 * @since 1.0.0
 */
class User_Form_Field extends Base_Field {



        public function get_name() {
            return 'frontend_user_field';
        }

        public function get_title() {
            return 'User Field';
        }

        public function get_icon() {
            return 'fa fa-user';
        }

        public function get_categories() {
            return [ 'general' ];
        }

        // searchable tags/keywords in Elementor (includes all field options)
        public function get_keywords() {
            return [
                'user',
                'username',
                'email',
                'bio',
                'password',
                'confirm_password',
                'display_name',
                'first_name',
                'last_name',
                'profile',
                'account',
            ];
        }

        protected function register_controls() {
            $this->start_controls_section(
                'section_content',
                [
                    'label' => 'Field Settings',
                ]
            );

            $this->add_control(
                'field_type',
                [
                    'label' => 'Field',
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        'username'         => 'Username',
                        'email'            => 'Email',
                        'bio'              => 'User Bio',
                        'password'         => 'Password',
                        'confirm_password' => 'Confirm Password',
                        'display_name'     => 'Display Name',
                        'first_name'       => 'First Name',
                        'last_name'        => 'Last Name',
                    ],
                    'default' => 'username',
                ]
            );

            $this->add_control(
                'label_text',
                [
                    'label' => 'Label (optional)',
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                ]
            );

            $this->add_control(
                'placeholder',
                [
                    'label' => 'Placeholder (optional)',
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => '',
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            $field = isset( $settings['field_type'] ) ? $settings['field_type'] : 'username';
            $label = trim( $settings['label_text'] );
            $placeholder = trim( $settings['placeholder'] );

            $current_user = wp_get_current_user();
            $value = '';

            switch ( $field ) {
                case 'username':
                    $value = $current_user->user_login ? $current_user->user_login : '';
                    $type_attr = 'text';
                    $name_attr = 'user_login';
                    break;
                case 'email':
                    $value = $current_user->user_email ? $current_user->user_email : '';
                    $type_attr = 'email';
                    $name_attr = 'user_email';
                    break;
                case 'bio':
                    $value = get_user_meta( $current_user->ID, 'description', true );
                    $type_attr = 'textarea';
                    $name_attr = 'description';
                    break;
                case 'display_name':
                    $value = $current_user->display_name ? $current_user->display_name : '';
                    $type_attr = 'text';
                    $name_attr = 'display_name';
                    break;
                case 'first_name':
                    $value = get_user_meta( $current_user->ID, 'first_name', true );
                    $type_attr = 'text';
                    $name_attr = 'first_name';
                    break;
                case 'last_name':
                    $value = get_user_meta( $current_user->ID, 'last_name', true );
                    $type_attr = 'text';
                    $name_attr = 'last_name';
                    break;
                case 'password':
                    $type_attr = 'password';
                    $name_attr = 'password';
                    $value = '';
                    break;
                case 'confirm_password':
                    $type_attr = 'password';
                    $name_attr = 'confirm_password';
                    $value = '';
                    break;
                default:
                    $type_attr = 'text';
                    $name_attr = 'user_field';
                    $value = '';
            }

            // Output label if set
            if ( $label ) {
                echo '<label for="' . esc_attr( $name_attr ) . '">' . esc_html( $label ) . '</label>';
            }

            // Render inputs
            if ( 'textarea' === $type_attr ) {
                echo '<textarea id="' . esc_attr( $name_attr ) . '" name="' . esc_attr( $name_attr ) . '" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea>';
            } else {
                // password field handling: confirm_password is its own input; password/widget supports either single password input or confirm field
                echo '<input id="' . esc_attr( $name_attr ) . '" name="' . esc_attr( $name_attr ) . '" type="' . esc_attr( $type_attr ) . '" value="' . ( 'password' === $type_attr ? '' : esc_attr( $value ) ) . '" placeholder="' . esc_attr( $placeholder ) . '">';
            }
        }

    }

    
