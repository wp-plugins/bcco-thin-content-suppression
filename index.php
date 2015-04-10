<?php
/**
 * Plugin Name: Thin Content Suppression
 * Plugin URI: https://thebc.co/
 * Description: We developed the Thin Content Suppression plugin for webmasters or SEOs that needed to suppress many pages of thin content at one time. This plugin was developed to NO INDEX FOLLOW or NOINDEX NOFOLLOW all pages with less than a certain number of words on a page. We added an exemption feature for Contact pages and others that are naturally “thin”.
 * Version: 1.0.0
 * Author: BCCO
 * Author URI: https://thebc.co/
 * License: GPL2
 */
register_activation_hook( __FILE__, 'thin_content_table_install' );
register_deactivation_hook( __FILE__, 'thin_content_table_drop' );
global $_db_version;
$_db_version = '1.0';
function thin_content_table_install(){
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'thin_general';
	$table_name2 = $wpdb->prefix . 'thin_pages';
	$charset_collate = $wpdb->get_charset_collate();
	$sql1 = "CREATE TABLE $table_name1 (
		id int(9) NOT NULL,
		meta_type int(9) NOT NULL,
		word_count int(9) NOT NULL
	)$charset_collate;";
	$sql2 = "CREATE TABLE $table_name2 (
		id int(9) NOT NULL AUTO_INCREMENT,
		page_id int(9) NOT NULL,
		PRIMARY KEY (id)
	)$charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql1 );	
	dbDelta( $sql2 );	
	add_option( '_db_version', $_db_version );
}
function thin_content_table_drop(){
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'thin_general';
	$table_name2 = $wpdb->prefix . 'thin_pages';
	$wpdb->query("DROP TABLE IF EXISTS $table_name1");
	$wpdb->query("DROP TABLE IF EXISTS $table_name2");
}
add_action( 'admin_menu', 'thin_content_menu' );
function thin_content_menu(){
	add_menu_page('Thin Content', 'Thin Content', 'manage_options', 'thin_settings', 'thin_content_settings', plugins_url( 'images/bcco.svg', __FILE__ ), 6 );
    add_submenu_page('thin_settings', 'Settings', 'Settings', 'manage_options', 'thin_settings' );
    add_submenu_page('thin_settings', 'About', 'About', 'manage_options', 'about', 'thin_content_about' );	
}
function thin_content_style() {
        wp_register_style( 'custom_wp_admin_css',plugins_url(  'css/main.css' , __FILE__ ), false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'thin_content_style' );
function thin_content_enqueue($hook) {
	wp_enqueue_script( 'my_custom_script',plugins_url(  'js/ZeroClipboard.js' , __FILE__ ));
}
add_action( 'admin_enqueue_scripts', 'thin_content_enqueue' );
function thin_content_settings(){
	include dirname(__FILE__)."/settings.php";
}
function thin_content_about(){
	include dirname(__FILE__)."/about.php";
}
add_shortcode("META_TAGS", "thin_content_meta_tags");
function thin_content_meta_tags($atts) {	
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'thin_general';
	$meta_type = $wpdb->get_results( "SELECT meta_type FROM $table_name1");
	$meta_type = $meta_type[0]->meta_type;
	$table_name2 = $wpdb->prefix . 'thin_pages';
	$pageID = $atts['page'];
	$pages_res = $wpdb->get_results( "SELECT count(*) as cnt FROM $table_name2 where page_id=".$pageID );
	if($pages_res[0]->cnt>0){
		if($meta_type==1){
			echo '<META NAME="ROBOTS" CONTENT="NOINDEX, FOLLOW">';
		}else if($meta_type==2){
			echo '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
		}
	}
}