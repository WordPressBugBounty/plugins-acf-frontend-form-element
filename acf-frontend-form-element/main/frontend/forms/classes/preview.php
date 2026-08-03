<?php
namespace Frontend_Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Frontend_Admin\Classes\Form_Preview' ) ) :

	class Form_Preview {

		/**
		 * How long a preview link stays valid, in seconds.
		 *
		 * @var int
		 */
		public $expiration = HOUR_IN_SECONDS;

		public function get_token( $post_id, $expiry ) {
			return hash_hmac( 'sha256', 'fea_preview|' . absint( $post_id ) . '|' . absint( $expiry ), wp_salt( 'auth' ) );
		}

		/**
		 * Build a signed, expiring URL that lets anyone (including guests)
		 * view an unpublished post in the theme template.
		 *
		 * @param int $post_id The draft/pending post to preview.
		 * @return string
		 */
		public function get_preview_url( $post_id ) {
			$expiry = time() + apply_filters( 'frontend_admin/form/preview_expiration', $this->expiration );

			$args = array(
				'p'            => $post_id,
				'preview'      => 'true',
				'_fea_preview' => $this->get_token( $post_id, $expiry ),
				'_fea_exp'     => $expiry,
			);

			$post_type = get_post_type( $post_id );
			if ( 'page' === $post_type ) {
				unset( $args['p'] );
				$args['page_id'] = $post_id;
			} elseif ( 'post' !== $post_type ) {
				$args['post_type'] = $post_type;
			}

			$url = add_query_arg( $args, home_url( '/' ) );

			return apply_filters( 'frontend_admin/form/submission_preview', $url, $GLOBALS['admin_form'] ?? array() );
		}

		/**
		 * Whether the current visitor may keep editing an unpublished draft that
		 * only exists because they previewed this form - covers guests and users
		 * without the edit_post capability. The post id can only reach the form
		 * through the encrypted _acf_objects blob minted by return_preview().
		 *
		 * @param int   $post_id
		 * @param array $form
		 * @return bool
		 */
		public function is_own_preview_draft( $post_id, $form ) {
			if ( empty( $form['id'] ) ) {
				return false;
			}
			if ( get_post_meta( $post_id, '_fea_preview_draft', true ) != $form['id'] ) {
				return false;
			}
			if ( ! in_array( get_post_status( $post_id ), array( 'draft', 'pending' ), true ) ) {
				return false;
			}
			return (int) get_post_field( 'post_author', $post_id ) === (int) get_current_user_id();
		}

		/**
		 * Let a request carrying a valid preview token view an unpublished post.
		 *
		 * Runs before WP_Query's singular status/404 check, so flipping the
		 * in-memory status to publish is enough — no capability grant, no DB write.
		 *
		 * @param array     $posts
		 * @param \WP_Query $query
		 * @return array
		 */
		public function show_draft( $posts, $query ) {
			if ( empty( $_GET['_fea_preview'] ) || empty( $_GET['_fea_exp'] ) ) {
				return $posts;
			}
			if ( ! $query->is_main_query() || ! $query->is_singular() || count( $posts ) !== 1 ) {
				return $posts;
			}

			$post   = $posts[0];
			$expiry = absint( $_GET['_fea_exp'] );

			if ( time() > $expiry ) {
				return $posts;
			}
			if ( ! in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
				return $posts;
			}
			if ( ! hash_equals( $this->get_token( $post->ID, $expiry ), (string) $_GET['_fea_preview'] ) ) {
				return $posts;
			}

			$post->post_status = 'publish';
			nocache_headers();

			return $posts;
		}

		public function __construct() {
			add_filter( 'posts_results', array( $this, 'show_draft' ), 10, 2 );
		}
	}

	fea_instance()->form_preview = new Form_Preview();

endif;
