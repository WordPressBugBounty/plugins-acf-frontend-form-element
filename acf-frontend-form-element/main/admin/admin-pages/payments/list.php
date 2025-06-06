<?php 


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
}

if( ! class_exists( 'FEA_Payments_List' ) ) :

	class FEA_Payments_List extends WP_List_Table {

		/** Class constructor */
		public function __construct() {

			parent::__construct( [
				'singular' => __( 'Payment', 'acf-frontend-form-element' ), //singular name of the listed records
				'plural'   => __( 'Payments', 'acf-frontend-form-element' ), //plural name of the listed records
				'ajax'     => false //does this table support ajax?
			] );

		}


		/**
		 * Retrieve payments data from the database
		 *
		 * @param int $per_page
		 * @param int $page_number
		 *
		 * @return mixed
		 */
		public static function get_payments( $per_page = 20, $page_number = 1 ) {

			global $wpdb;

			$sql = "SELECT * FROM {$wpdb->prefix}fea_payments";

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				$sql .= $wpdb->prepare( ' ORDER BY ' . sanitize_sql_orderby( $_REQUEST['orderby'] ) );
				$sql .= ! empty( $_REQUEST['order'] ) ? 
					$wpdb->prepare( ' ' . esc_sql( $_REQUEST['order'] ) )
					:
					' ASC';
			}else{
				$sql .= ' ORDER BY created_at DESC' ;
			}

			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			return $result;
		}


		/**
		 * Delete a payment record.
		 *
		 * @param int $id payment ID
		 */
		public static function delete_payment( $id ) {
			global $wpdb;

			$wpdb->delete(
				"{$wpdb->prefix}fea_payments",
				['id' => $id ],
				['%d']
			);
		}


		/**
		 * Returns the count of records in the database.
		 *
		 * @return null|string
		 */
		public static function record_count() {
			global $wpdb;

			$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}fea_payments";

			return $wpdb->get_var( $sql );
		}


		/** Text displayed when no payment data is available */
		public function no_items() {
			_e( 'No payments avaliable.', 'acf-frontend-form-element' );
		}


		/**
		 * Render a column when no column specific method exist.
		 *
		 * @param array $item
		 * @param string $column_name
		 *
		 * @return mixed
		 */
		public function column_default( $item, $column_name ) {
			switch( $column_name ){
				case 'amount':
					return $item['amount'] . ' ' . $item['currency'];
				case 'created_at':
					$time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
					return date( $time_format, strtotime( $item[ $column_name ] ) );
				case 'method':
					if( $item['method'] == 'stripe' ){
						return '<img width="50" src="'. FEAP_URL . 'assets/images/stripe.png"/>';
					}	
					if( $item['method'] == 'paypal' ){
						return '<img width="50" src="'. FEAP_URL . 'assets/images/paypal.png"/>';
					}	
				case 'user':
					$user = get_user_by( 'ID', $item[ $column_name ] );
					if( is_object( $user ) ){
						return $user->display_name . ' (' . $user->user_login . ')';
					}
				default:
					return $item[ $column_name ];
			}
		}


		/**
		 * Gets the name of the default primary column.
		 *
		 * @since 4.3.0
		 *
		 * @return string Name of the default primary column, in this case, 'title'.
		 */
		protected function get_default_primary_column_name() {
			return 'description';
		}


		/**
		 *  Associative array of columns
		 *
		 * @return array
		 */
		function get_columns() {
			$columns = [
				'description'  => __( 'Description', 'acf-frontend-form-element' ),
				'external_id' => __( 'External ID', 'acf-frontend-form-element' ),
				'user' => __( 'User', 'acf-frontend-form-element' ),
				'method' => __( 'Processor', 'acf-frontend-form-element' ),
				'amount'    => __( 'Amount', 'acf-frontend-form-element' ),
				'created_at' => __( 'Date', 'acf-frontend-form-element' ),
			];

			return $columns;
		}


		/**
		 * Handles data query and filter, sorting, and pagination.
		 */
		public function prepare_items() {

			$this->_column_headers = $this->get_column_info();

			/** Process bulk action */
			$this->process_bulk_action();

			$perpage     = $this->get_items_per_page( 'payments_per_page', 20 );
			$current_page = $this->get_pagenum();
			$total_items  = self::record_count();

			$this->set_pagination_args( [
				'total_items' => $total_items, //WE have to calculate the total number of items
				'per_page'    => $perpage //WE have to determine how many items to show on a page
			] );

			$this->items = self::get_payments( $perpage, $current_page );
			
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
		
		}

		public function process_bulk_action() {

			//Detect when a bulk action is being triggered...
			if ( 'delete' === $this->current_action() ) {

				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				if ( ! wp_verify_nonce( $nonce, 'delete_payment' ) ) {
					die( 'Go get a life script kiddies' );
				}
				else {
					self::delete_payment( absint( $_GET['payment'] ) );

							// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
							// add_query_arg() return the current url
							wp_redirect( esc_url_raw(add_query_arg()) );
					exit;
				}

			}

			// If the delete bulk action is triggered
			if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
				|| ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
			) {

				$delete_ids = esc_sql( $_POST['bulk-delete'] );

				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					self::delete_payment( $id );

				}

				// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
					// add_query_arg() return the current url
					wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}
		}

	}

	fea_instance()->payments_list = new FEA_Payments_List;

endif;
