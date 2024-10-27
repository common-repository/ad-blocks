<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Settings Class.
 * Get general settings
 * @class 		ADB_Settings
 * @version		1.0.1
 * @author 		Sergey Kravchenko
 */

class ADB_Settings {
    
    //// option name
    static $option_name = 'adb_settings';
    
    //// option menu slug
    static $option_menu_slug = 'adb_settings';
    
    ///// general settings array
    public static $settings = array();
    
///////////////////////////////////////
    
    public static function init() {
        
        self::$settings = wp_parse_args( get_option(self::$option_name), array(
            'date_format' => 'd/m/Y',
            'classic_editor' => 0,
        ));
             
	}            
        
///////////////////////////////////////
    /**
	 * Get option by name.
     * 
     * @param string $setting_name
     * @param mixed $default
     * 
     * @return mixed
	 */
    public static function get_option($setting_name, $default = '') {
        
        return isset(self::$settings[$setting_name]) ? self::$settings[$setting_name] : $default;
        
    }                     
    
///////////////////////////////////////
}

ADB_Settings::init();
