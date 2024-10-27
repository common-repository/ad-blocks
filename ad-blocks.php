<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Ad Blocks
 * Plugin URI: https://wp-aptools.com/
 * Description: Publish and manage advertising blocks with Google AdSense, Amazon or any custom html, in posts and sidebars using shortcodes and widgets
 * Version:           1.0.3
 * Author:            Sergey Kravchenko
 * Author URI: https://profiles.wordpress.org/sergeytraveler
 * Requires at least: 4.6.1
 * Requires PHP: 5.4
 * Tested up to: 5.1.0
 * Text Domain: ad-blocks
 * Domain Path: /languages/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
 
 //// min required WP 4.6.1, PHP 5.4

if ( ! defined( 'ABSPATH' ) )
	exit;

define( 'ADB_VERSION', '1.0.3' );
define( 'ADB_PLUGIN_SLUG', 'ad-blocks' );
define( 'ADB_PLUGIN', __FILE__ );
define( 'ADB_PLUGIN_DIR', untrailingslashit( dirname( ADB_PLUGIN ) ) );
define( 'ADB_TEXTDOMAIN', 'ad-blocks' );

if ( file_exists(  ADB_PLUGIN_DIR . '/includes/vendors/cmb2/init.php' ) ) {
  require_once ADB_PLUGIN_DIR . '/includes/vendors/cmb2/init.php';
  
  if ( file_exists(  ADB_PLUGIN_DIR . '/includes/vendors/cmb2-conditionals/cmb2-conditionals.php' ) ) {
     require_once ADB_PLUGIN_DIR . '/includes/vendors/cmb2-conditionals/cmb2-conditionals.php';
  }
}

include_once ADB_PLUGIN_DIR . '/includes/class-install.php';

include_once ADB_PLUGIN_DIR . '/includes/class-functions.php';

include_once ADB_PLUGIN_DIR . '/includes/class-settings.php';

include_once ADB_PLUGIN_DIR . '/includes/class-calendar-functions.php';

include_once ADB_PLUGIN_DIR . '/includes/class-post-types.php';

include_once ADB_PLUGIN_DIR . '/includes/widgets/class-adblocks.php';

///// admin

if ( is_admin() ) {
    
    include_once ADB_PLUGIN_DIR . '/includes/class-settings-admin.php';
    
    include_once ADB_PLUGIN_DIR . '/includes/class-cmb2-admin.php';
    
    include_once ADB_PLUGIN_DIR . '/includes/class-posts-admin.php';
    
}
 
    