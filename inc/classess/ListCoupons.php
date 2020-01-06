<?php 
class ListCoupons extends WP_List_Table {
	protected $fetch_coupons;
	public function __construct() {
		global $wpdb;
		/* Set W_LIST_TABLE defaults */
		parent::__construct( array(
			'singular' 	=> 'Coupon',     // Singular name of the listed records.
			'plural'   	=> 'Coupons',    // Plural name of the listed records.
			'ajax'     	=> false,       // Does this table support ajax?
		) );
		$coupon_sql = "SELECT * FROM $wpdb->coupons";
		$coupons = $wpdb->get_results( $coupon_sql, ARRAY_A );
		$this->fetch_coupons = $coupons;
	}

	/* Set Table Columns Title and View */
	public function get_columns(){
		$columns = array(
			'cb'       		=> '<input type="checkbox" />', // Render a checkbox instead of text.
			'title'   		=> _x( 'Coupon Code', 'Column label', 'wp-coupons' ),
			'coupon_desc'   => _x( 'Description', 'Column label', 'wp-coupons' ),
			'coupon_type'   => _x( 'Type', 'Column label', 'wp-coupons' ),
			'coupon_amount' => _x( 'Amount', 'Column label', 'wp-coupons' ),
			'expiry_date' 	=> _x( 'Expire', 'Column label', 'wp-coupons' ),
		);
		return $columns;
	}
	/**
	 *	Sortable Columns 
	 */
	protected function get_sortable_columns(){
		$sortable_columns = array(
			'title'   		=> array( 'title', false ),
			'coupon_desc'   => array( 'coupon_desc', false ),
			'coupon_type'   => array( 'coupon_type', false ),
			'coupon_amount' => array( 'coupon_amount', false ),
			'expiry_date' 	=> array( 'expiry_date', false ),
		);
		return $sortable_columns;
	}
	/** 
	 *	default Columns
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'coupon_desc':
			case 'coupon_type':
			case 'coupon_amount':
			case 'expiry_date':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}
	/**
	 * Get value for checkbox column.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item['id']
		);
	}
	// Get title column value
	protected function column_title( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.
		// Build edit row action.
		$edit_query_args = array(
			'page'   => $page,
			'action' => 'edit',
			'coupon'  => $item['id'],
		);
		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editcoupon_' . $item['id'] ) ),
			_x( 'Edit', 'List table row action', 'wp-coupons' )
		);
		// Build delete row action.
		$delete_query_args = array(
			'page'   => $page,
			'action' => 'delete',
			'coupon'  => $item['id'],
		);
		$actions['delete'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletecoupon_' . $item['id'] ) ),
			_x( 'Delete', 'List table row action', 'wp-coupons' )
		);
		// Build view row action.
		$view_query_args = array(
			'page'   => $page,
			'action' => 'view',
			'coupon'  => $item['id'],
		);
		$actions['view'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $view_query_args, 'admin.php' ), 'viewcoupon_' . $item['id'] ) ),
			_x( 'View', 'List table row action', 'wp-coupons' )
		);
		// Return the title contents.
		return sprintf( '%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
			$item['title'],
			$item['id'],
			$this->row_actions( $actions )
		);
	}
	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Delete', 'List table bulk action', 'wp-coupons' ),
		);
		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @see $this->prepare_items()
	 */
	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {
			$delete_id = $_REQUEST['coupon'];
			if( is_array( $delete_id ) ){
				_e('You are deleting multiple items');
			}
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		global $wpdb;
		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;
		
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		
		$this->_column_headers = array( $columns, $hidden, $sortable );
		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();
		/*
		 * GET THE COUPONS DATA!
		 */
		$data = $this->fetch_coupons;
		
		usort( $data, array( $this, 'usort_reorder' ) );
		
		$current_page = $this->get_pagenum();
		
		$total_items = count( $data );
		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;
		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Callback to allow sorting of Coupon Data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title'; // WPCS: Input var ok.
		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.
		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
		return ( 'asc' === $order ) ? $result : - $result;
	}
}