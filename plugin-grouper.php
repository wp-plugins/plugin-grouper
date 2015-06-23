<?php
/**
 * Plugin Name: Plugin Grouper
 * Plugin URI: http://www.sujinc.com/
 * Description: Too many plugins bother you? Make them into group!
 * Version: 1.1.0
 * Author: Sujin 수진 Choi
 * Author URI: http://www.sujinc.com/
 * License: GPLv2 or later
 * Text Domain: plugin-grouper
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

# Definitions
if ( !defined( 'PIGPR_PLUGIN_NAME' ) ) {
	$basename = trim( dirname( plugin_basename( __FILE__ ) ), '/' );
	if ( !is_dir( WP_PLUGIN_DIR . '/' . $basename ) ) {
		$basename = explode( '/', $basename );
		$basename = array_pop( $basename );
	}

	define( 'PIGPR_PLUGIN_NAME', $basename );

	if ( !defined( 'PIGPR_PLUGIN_DIR' ) )
		define( 'PIGPR_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . PIGPR_PLUGIN_NAME );

	if ( !defined( 'PIGPR_TEMPLATE_DIR' ) )
		define( 'PIGPR_TEMPLATE_DIR', PIGPR_PLUGIN_DIR . '/templates/' );

	if ( !defined( 'PIGPR_ASSETS_URL' ) )
		define( 'PIGPR_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
}

# Load Classes
include_once( PIGPR_PLUGIN_DIR . '/classes/class.init.php');




