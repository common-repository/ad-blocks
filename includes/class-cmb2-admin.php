<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_CMB2_admin Class.
 * 
 * @class 		ADB_CMB2_admin
 * @version		1.0.0
 * @author 		Sergey Kravchenko
 */

class ADB_CMB2_admin {
    
//////////////////////////////
    /**
	 * Hook in tabs.
	 */
    public static function init() {       
        
        add_filter( 'cmb2_render_shortcode_key_name', array( __CLASS__, 'cmb2_shortcode_key_name'), 10, 5 );
        add_filter( 'cmb2_sanitize_shortcode_key_name', array( __CLASS__, 'cmb2_sanitize_shortcode_key_name'), 10, 5 );
        
        add_action( 'cmb2_admin_init', array( __CLASS__, 'metabox_adblock' ), 10, 1);
        
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueued_assets' ) );
                
	}
     
//////////////////////////////
    /**
	 * Enqueue assets admin.
	 */
    public static function enqueued_assets() {
        
     global $current_screen;   
        
     wp_enqueue_script( 'adb-admin-cmb2-js', plugins_url( "js/admin/adb-admin-cmb2.js", ADB_PLUGIN ), array('jquery'), '1.0', true );
     wp_localize_script( 'adb-admin-cmb2-js', 'adb_cmb2_lst', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'date_format' => ADB_Settings::$settings['date_format'] == 'd/m/Y' ? 'dd/mm/yy' : 'mm/dd/yy',
            'nonce' => wp_create_nonce(ADB_Post_types::$nonce_title),
         )
     );
     
     wp_enqueue_style( 'adb-admin-cmb2', plugins_url( "css/admin/adb-admin-cmb2.css", ADB_PLUGIN ));  
     
     wp_enqueue_script('jquery-ui-datepicker');
     wp_enqueue_style('jquery-ui-style', plugins_url( "css/jquery-ui.css", ADB_PLUGIN ));
      
     }                     

///////////////////////////////          
     /**
	 * Register text field
	 */ 
     public static function cmb2_shortcode_key_name($field, $value, $object_id, $object_type, $field_type ){
     
      $output = $field_type->input( array(
			'name'  => $field_type->_name(),
			'id'    => $field_type->_id(),
			'value' => $value,
		) );
    
      echo $output;
     }

///////////////////////////////     
     /**
	 * Sanitize field
	 */ 
     public static function cmb2_sanitize_shortcode_key_name( $override_value, $value, $object_id, $field_args, $sanitizer_object ) {
        
        $value = sanitize_title($value);
        
        return $value;
        
     }              

///////////////////////////////////////
    /**
	 * Register extra fields.
	 */
    public static function metabox_adblock() {
        
      $cmb = new_cmb2_box( array(
        'id'            => 'metabox_adblock',
        'title'         => __( 'Shortcode options', ADB_TEXTDOMAIN ),
        'object_types'  => array( ADB_Post_types::$adblock_post_type, ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // Keep the metabox closed by default
      ) );
      
      $cmb->add_field( array(
         'name' => __( 'Shortcode name param', ADB_TEXTDOMAIN ),
         'id'   => 'shortcode_key_name',
         'type'    => 'shortcode_key_name',        
         'attributes'  => array(
           'required'    => 'required',
           'class' => 'medium-text',
           ),
      ));
      
      $cmb = new_cmb2_box( array(
        'id'            => 'metabox_adblock_auto',
        'title'         => __( 'Auto posting', ADB_TEXTDOMAIN ),
        'object_types'  => array( ADB_Post_types::$adblock_post_type, ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
        // 'cmb_styles' => false, // false to disable the CMB stylesheet
        // 'closed'     => true, // Keep the metabox closed by default
      ) );
         
      $cmb->add_field( array(
         'name' => __( 'Auto add to posts', ADB_TEXTDOMAIN ),
         'id'   => 'add_to_posts',
         'type'    => 'radio_inline',        
         'options'  => array(
           0  => __( 'No', ADB_TEXTDOMAIN ),
           1 => __( 'Yes', ADB_TEXTDOMAIN ),
           ),
         'default' => 0  
      ));
      
      $cmb->add_field( array(
         'name' => __( 'Insert location', ADB_TEXTDOMAIN ),
         'id'   => 'add_location',
         'type'    => 'radio_inline',        
         'options'  => array(
           0  => __( 'Before content', ADB_TEXTDOMAIN ),
           1 => __( 'After content', ADB_TEXTDOMAIN ),
           ),
         'default' => 0  
      ));
      
      $cmb->add_field( array(
         'name' => __( 'Post types', ADB_TEXTDOMAIN ),
         'id'   => 'add_post_types',
         'type'    => 'multicheck',        
         'options'  => self::get_post_types_options(), 
      ));   
    
   }
   
//////////////////////////////
    /**
	 * Get post types option list.
     * 
     * @return array
	 */
    public static function get_post_types_options() {
        
        $output = array();
        
        $args = array(
          'public'   => true,
        );
        
        $post_types = get_post_types( $args, 'objects');
        
        foreach ( $post_types as $post_type ) {
            
            if ( !$post_type->_builtin || ($post_type->_builtin && ($post_type->name == 'post' || $post_type->name == 'page') )){
                $output[ $post_type->name ] = $post_type->labels->singular_name;
            }
        }
        
        return $output;
    
    }           

//////////////////////////////    

}

ADB_CMB2_admin::init();
