<?php 
class Admin extends CouponsInit{
	public function __construct(){
		$this->admin_hooks();
		parent::__construct();
	}
	// Hooks For admin
	public function admin_hooks(){
		add_action( 'admin_menu', array( &$this, 'register_coupons_main_menu' ) );
		add_action( 'admin_init', array( &$this, 'save_coupons' ) );
		add_filter( "plugin_action_links_" . COUPON_PLUGIN_NAME, array( &$this, 'coupons_page_link' ) );
	}
	/* Register Admin Page */
	public function register_coupons_main_menu(){
		/* Main Menu */
		add_menu_page( __( 'Coupons', 'wp-coupons' ), __( 'Coupons', 'wp-coupons' ), 'manage_options', 'wp-coupons', array( &$this, 'coupons_main_menu_callback' ), 'dashicons-tickets', '55' );
		/* Add New Coupon Menu */
		add_submenu_page( 'wp-coupons', __( 'Add New Coupon', 'wp-coupons' ), __( 'Add New Coupon', 'wp-coupons' ), 'manage_options', 'add-new-coupon', array( &$this, 'coupons_add_new_callback' ) );
	}

	public function coupons_main_menu_callback(){
		// Admin Page Title 
		global $title;
		?>
			<div class="wrap">
				<?php
				switch ( $_REQUEST['action'] ) {
				 	case 'view':
				 		?>
					 		<h1 class="wp-heading-inline"><?php _e('View Coupon'); ?></h1>
					 	<?php
				 		break;
			 		case 'edit':
				 		?>
					 		<h1 class="wp-heading-inline"><?php _e('Edit Coupon'); ?></h1>
					 	<?php
				 		break;
			 		case 'delete':
				 		?>
					 		<h1 class="wp-heading-inline"><?php _e('Delete Coupon'); ?></h1>
					 	<?php
				 		break;
				 	
				 	default:
				 		?>
					 		<h1 class="wp-heading-inline"><?php echo $title; ?></h1>
							<a href="<?php echo admin_url( 'admin.php?page=add-new-coupon' ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-coupons') ?></a>
							<hr class="wp-header-end">
							<form id="coupons-filter" method="post">
								
								<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
								
								<?php

									$list_coupons = new ListCoupons();
									$list_coupons->prepare_items(); 
									$list_coupons->search_box( __( "Search", 'wp-coupons' ), 'coupon' ); 
									$list_coupons->display();
								?>
							</form>
				 		<?php
				 		break;
				 } 
				?>
				
			</div>
		<?php
	}

	public function coupons_add_new_callback(){
		// Admin Page Title 
		global $title;
		?>
			<div class="wrap">
				<div class="coupon-container">
					<h1 class="wp-heading-inline"><?php echo $title; ?></h1>
					<form method="post" action="<?php echo admin_url( 'admin.php?page=add-new-coupon' ); ?>" class="form coupon-form">
						<div class="coupon-row divider">  
							<div class="coupon-col-4"> 
								<label for="coupon-code"><?php _e('Coupon Code:', 'wp-coupons') ?></label>
							</div>
							<div class="coupon-col-8">
								<input type="text" name="title" id="coupon-code" class="form-input" placeholder="<?php _e('Coupon Code', 'wp-coupons') ?>" autocomplete="off">
							</div>
						</div>
						<div class="coupon-row divider">  
							<div class="coupon-col-4"> 
								<label for="coupon-desc"><?php _e('Coupon Description:', 'wp-coupons') ?></label>
							</div>
							<div class="coupon-col-8">
								<textarea name="coupon_desc" id="coupon-desc" class="form-input" placeholder="<?php _e('Coupon Description', 'wp-coupons') ?>"></textarea>
							</div>
						</div>
						<div class="coupon-row divider">  
							<div class="coupon-col-4"> 
								<label for="coupon-expiry_date"><?php _e('Coupon Expire:', 'wp-coupons') ?></label>
							</div>
							<div class="coupon-col-8">
								<input type="text" name="expiry_date" id="coupon-expiry_date" class="form-input" placeholder="<?php _e('Coupon Expire', 'wp-coupons') ?>" autocomplete="off">
							</div>
						</div>
						<div class="coupon-row divider">  
							<div class="coupon-col-4"> 
								<label for="coupon-type"><?php _e('Coupon Type:', 'wp-coupons') ?></label>
							</div>
							<div class="coupon-col-8">
								<select name="coupon_type" id="coupon-type" class="form-input">
									<option value="fixed"><?php _e('Fixed', 'wp-coupons') ?></option>
									<option value="percent"><?php _e('Percent', 'wp-coupons') ?></option>
								</select>
							</div>
						</div>
						<div class="coupon-row divider">  
							<div class="coupon-col-4"> 
								<label for="coupon-amount"><?php _e('Coupon Amount:', 'wp-coupons') ?></label>
							</div>
							<div class="coupon-col-8">
								<input type="text" name="coupon_amount" id="coupon-amount" class="form-input" placeholder="<?php _e('Coupon Amount', 'wp-coupons') ?>" autocomplete="off">
							</div>
						</div>
						<div class="coupon-row divider">  
							<div class="coupon-col-4"> 
								<label for="coupon-status"><?php _e('Coupon status:', 'wp-coupons') ?></label>
							</div>
							<div class="coupon-col-8">
								<select name="status" id="coupon-status" class="form-input">
									<option value="active"><?php _e('Active', 'wp-coupons') ?></option>
									<option value="inactive"><?php _e('Inactive', 'wp-coupons') ?></option>
								</select>
							</div>
						</div>
						<div class="coupon-row space-top">  
							<div class="coupon-col-12">
								<button type="submit" name="add_new_coupon" class="button button-primary"><?php _e('Save Coupon', 'wp-coupons') ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<?php
	} 

	public function coupons_page_link( $links ) {

		$links[] = '<a href="' .
		admin_url( 'admin.php?page=add-new-coupon' ) .
		'">' . __( 'Add Coupons', 'wp-coupons' ) . '</a>';

	    return $links;
	}

	public function save_coupons(){
		global $wpdb;
		if( isset( $_POST['add_new_coupon'] ) ){
			/* Unset Button Action */
			unset($_POST['add_new_coupon']);
			/* Insert New Coupon */
			$wpdb->insert( $this->coupon_table, $_POST );
			if( !empty( $wpdb->insert_id ) ):
				$inserted_id = $wpdb->insert_id;
				add_action( 'admin_notices', array( &$this, 'coupon_save_msg__success' ) );	
				add_filter( 'coupon_view_text', function( $view_link, $view_coupon_text ) use($inserted_id){
					$new_view_link = $view_link . $inserted_id;
					$view_coupon_text =  '<a href="' . $new_view_link . '">'. __( 'View Coupon', 'wp-coupons' ) .'</a>';
					return $view_coupon_text;
				}, 10, 2);
			else: 
				add_action( 'admin_notices', array( &$this, 'coupon_save_msg__error' ) );
			endif;
		} 
	}
	public function coupon_save_msg__success() {
		$view_link = admin_url( 'admin.php?page=wp-coupons&action=view&coupon=' );
		$view_coupon_text = '<a href="javascript:void(0)">'. __( 'View Coupon', 'wp-coupons' ) .'</a>';
	    $view_coupon_text = apply_filters('coupon_view_text', $view_link, $view_coupon_text);
	    ?>
		    <div class="notice notice-success is-dismissible">
		        <p><?php _e( 'Coupon Succefully Saved ! ', 'wp-coupons' ); echo $view_coupon_text; ?></p>
		    </div>
	    <?php
	}
	public function coupon_save_msg__error() {
	    ?>
		    <div class="notice notice-error is-dismissible">
		        <p><?php _e( 'Coupon couldn\'t Saved ! ', 'wp-coupons' );?></p>
		    </div>
	    <?php
	}
								
}
