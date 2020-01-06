<?php
/*
Plugin Name: WP Coupons
Author: Vishal Tanwar
Description: This plugin to create coupons
version: 1.0.0
Text Domain: wp-coupons	
*/
if ( !defined( 'ABSPATH' ) ) exit;

define( 'COUPON_PLUGIN_DIR', __DIR__ );

define( 'COUPON_PLUGIN_FILE',  __FILE__ );

define( 'COUPON_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

define( 'COUPON_PLUGIN_NAME' , plugin_basename( __FILE__ ) );

/**
 * @method WP_LIST_TABLE Class 
 */
if ( ! class_exists( 'WP_List_Table' ) ) {

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

include_once COUPON_PLUGIN_DIR . '/inc/classess/ListCoupons.php';
include_once COUPON_PLUGIN_DIR . '/inc/classess/CouponsInit.php';
include_once COUPON_PLUGIN_DIR . '/inc/classess/Admin.php';
// Initiate plugin
$coupon_admin = new Admin();

function coupons_custom_column( $columns ){
	$columns['metavalue'] = 'Status';
	return $columns;
}
add_filter( 'manage_wp-coupons_posts_columns', 'coupons_custom_column' );


