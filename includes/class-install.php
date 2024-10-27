<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Install Class.
 * 
 * @class 		ADB_Install
 * @version		1.0.0
 * @author 		Sergey Kravchenko
 */

class ADB_Install {
    
//////////////////////////////
    /**
	 * Hook in tabs.
	 */
    public static function init() {
        
        register_activation_hook( ADB_PLUGIN, array( __CLASS__, 'activation') );
        register_deactivation_hook( ADB_PLUGIN, array( __CLASS__, 'deactivation') );
        
        add_action( 'init', array( __CLASS__, 'load_textdomain' ));
        
	}
    
//////////////////////////////
	/**
	 * Load Localisation files.
	 */
	public static function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), ADB_TEXTDOMAIN );

		load_textdomain( ADB_TEXTDOMAIN, WP_LANG_DIR . '/ad-blocks/ad-blocks-' . $locale . '.mo' );
		load_plugin_textdomain( ADB_TEXTDOMAIN, false, ADB_PLUGIN_DIR . '/languages' );
        
        return;
	}
        
//////////////////////////////

    /**
	 * Install post types, etc.
	 */
    public static function activation() {
        
       global $wpdb;
       
       ADB_Post_types::register_post_types();
       flush_rewrite_rules();
       
       return;
       
	}
    
//////////////////////////////

    /**
	 * Flush rewrite rules, etc.
	 */
    public static function deactivation() {
       
       flush_rewrite_rules();
       
       return;
       
	}
    
////////////////////////////
    /**
	 * Create front-end page.
	 */
	public static function create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	   
    global $wpdb;
    
    $settings = get_option(ADB_Settings::$option_name);
    $option_value = isset($settings[$option]) ? absint($settings[$option]) : 0;
    
    if ( $option_value && get_post( $option_value ) ){
      return;
    }  
      
    $page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = '$slug' LIMIT 1;");
    
    if ( $page_found ) :
      if ( ! $option_value ){
        $settings[$option] = $page_found;
        update_option( ADB_Settings::$option_name, $settings );
      }
      return;  
    endif;
    
    $page_data = array(
          'post_status' 		=> 'publish',
          'post_type' 		=> 'page',
          'post_author' 		=> 1,
          'post_name' 		=> $slug,
          'post_title' 		=> $page_title,
          'post_content' 		=> $page_content,
          'post_parent' 		=> $post_parent,
          'comment_status' 	=> 'closed'
      );
    $page_id = wp_insert_post( $page_data );
      
    $settings[$option] = $page_id;
    update_option( ADB_Settings::$option_name, $settings );
    
    return;
   }
    
//////////////////////////////    
    
}

ADB_Install::init();
