<?php
/*
Plugin Name: MyBookTable
Plugin URI: http://www.mybooktable.com
Description: A simple store plugin for books, allowing you to integrate with external shopping carts and booktable websites.
Author: Castle Media Group
Version: 0.3.0
*/

require_once("includes/functions.php");
require_once("includes/setup.php");
require_once("includes/templates.php");
require_once("includes/buybuttons.php");
require_once("includes/admin_pages.php");
require_once("includes/post_types.php");
require_once("includes/metaboxes.php");
require_once("includes/breadcrumbs.php");
require_once("includes/seo.php");



/*---------------------------------------------------------*/
/* Activate Plugin                                         */
/*---------------------------------------------------------*/

function mbt_activate() {
	//flush rewrite rules
	mbt_create_post_types_and_taxonomies();
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
register_activation_hook(__FILE__, 'mbt_activate');



/*---------------------------------------------------------*/
/* Initialize Plugin                                       */
/*---------------------------------------------------------*/

function mbt_init() {
	mbt_upgradecheck();
	mbt_load_settings();

	if(isset($_GET['mbt_uninstall'])) {
		return mbt_uninstall();
	}

	add_action('admin_menu', 'mbt_add_admin_pages', 9);
	add_action('init', 'mbt_create_post_types_and_taxonomies');
	add_action('admin_init', 'mbt_admin_init');
}
add_action('plugins_loaded', 'mbt_init');