<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Posts_admin Class.
 * 
 * @class 		ADB_Posts_admin
 * @version		1.0.0
 * @author 		Sergey Kravchenko
 */

class ADB_Posts_admin {
    
///////////////////////////////////////    
    public static function init() {
        
        add_filter( 'manage_'.ADB_Post_types::$adblock_post_type.'_posts_columns', array( __CLASS__, 'post_adblock_table_head'));
        add_action( 'manage_'.ADB_Post_types::$adblock_post_type.'_posts_custom_column', array( __CLASS__, 'post_adblock_table_content'), 10, 2 );
    
	}

/////////////////////
    /**
	 * Add post custom column heads.
     * 
     * @param array $defaults
     * @return array
	 */
    public static function post_adblock_table_head( $defaults ) {
        
       unset($defaults['post_type']);
       unset($defaults['date']);
       
       $defaults['shortcode']   = __('Shortcode', ADB_TEXTDOMAIN);
       $defaults['date']   = __('Date', ADB_TEXTDOMAIN);
    
       return $defaults;
    }

///////////////////////////////////
    /**
	 * Add post custom column content.
     * 
     * @param string $column_name
     * @param int $post_id
     * @return array
	 */
    public static function post_adblock_table_content( $column_name, $post_id ) {
    
      if ($column_name == 'shortcode') {
        
        $shortcode_key_name = get_post_meta($post_id, 'shortcode_key_name', 1);
                
        echo $shortcode_key_name ? '['.ADB_Post_types::$shortcode_adblock_name.' name="'.$shortcode_key_name.'"]' : '';
      }
    
    }     
    
/////////////////////////////////////
    
}

ADB_Posts_admin::init();
