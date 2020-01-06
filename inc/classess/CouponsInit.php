<?php 
class CouponsInit{
	public $coupon_table;
	public function __construct(){
		global $wpdb;
		$wpdb->coupons = $wpdb->prefix . 'coupons';
		$this->coupon_table = $wpdb->prefix . 'coupons';
		$this->hook();
	}

	public function hook(){
		register_activation_hook( COUPON_PLUGIN_FILE , array( &$this, 'activate_coupons'));
		add_action( 'admin_enqueue_scripts', array( &$this, 'coupons_admin_style_n_scripts' ) );
	}

	public function coupons_admin_style_n_scripts(){
		wp_enqueue_style('admin-coupon-style', COUPON_PLUGIN_URL . 'inc/css/admin-coupon-style.css?v='.rand(100,999), array(), NULL );
		// Load Pre Registered WP datepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );
		// Load my Own Script
		wp_enqueue_script( 'admin-coupon-script', COUPON_PLUGIN_URL . 'inc/js/admin-coupon-script.js?v='.rand(100,999), array(), NULL, true );
	}

	/* Create Table In Database IF Not Exist */
	public function activate_coupons(){
		$sql = "CREATE TABLE IF NOT EXISTS $this->coupon_table (
			id int(11) NOT NULL AUTO_INCREMENT,
			title VARCHAR(255) NOT NULL,
			coupon_desc VARCHAR(255) NOT NULL,
			coupon_type VARCHAR(255) NOT NULL,
			coupon_amount VARCHAR(255) NOT NULL,
			expiry_date DATETIME NOT NULL,
			created_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',	
			UNIQUE KEY id (id)
			);";
	 
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
	}

}

