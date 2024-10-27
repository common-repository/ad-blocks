<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Settings_admin Class.
 * Manage general settings
 * 
 * @class 		ADB_Settings_admin
 * @version		1.0.0
 * @author 		Sergey Kravchenko
 */

class ADB_Settings_admin {
    
///////////////////////////////////////    
    public static function init() {
        
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 10 );
        add_action( 'admin_init', array( __CLASS__, 'settings_page_init' ), 10 );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueued_assets' ), 10 );
     
	}
///////////////////////////////////////
    /**
	 * Enqueue assets.
	 */
    public static function enqueued_assets() {
        
     if (isset($_GET['page']) && $_GET['page'] == ADB_Settings::$option_menu_slug){   
     
         wp_enqueue_style( 'wp-color-picker');
         wp_enqueue_script( 'wp-color-picker');
        if(function_exists( 'wp_enqueue_media' )){
         wp_enqueue_media();
         } else {
           wp_enqueue_style('thickbox');
           wp_enqueue_script('media-upload');
           wp_enqueue_script('thickbox');
           }
           
         wp_enqueue_style( 'adb-admin-settings', plugins_url( "css/admin/adb-admin-settings.css", ADB_PLUGIN ));  
      }
     }          
     
///////////////////////////////////////    
    /**
	 * Add Settings admin page to menu.
	 */
    public static function add_settings_page(){
        
        add_menu_page(
            __('Ad Blocks Settings', ADB_TEXTDOMAIN),
            __('Ad Blocks Settings', ADB_TEXTDOMAIN),
            'manage_options',
            ADB_Settings::$option_menu_slug,
            array( __CLASS__, 'create_settings_page' ),
            '',
            26
        );
        
    }
    
///////////////////////////////////////    
    /**
     * Settings page callback
     */
    public static function create_settings_page()
    {
        ?>
        <div class="wrap adb-settings-wrap">
            <h2><?php echo __('Ad Blocks Settings', ADB_TEXTDOMAIN); ?></h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( ADB_Settings::$option_menu_slug );
                self::do_settings_sections( ADB_Settings::$option_menu_slug );
                submit_button();
            ?>
            </form>
        </div>

        <script>
(function( $ ) {
	// Add Color Picker to all inputs that have 'color-field' class
	$(function() {
	$('.color-field').wpColorPicker();
	});
})( jQuery );
       </script>

        <?php
    }
    
////////////////////////////////////////////////

    /**
     * Custom do settings with tabs support
     * 
     * @param string $page
     * @return
     */
    public static function do_settings_sections( $page ) {
        
      global $wp_settings_sections, $wp_settings_fields;
 
      if ( ! isset( $wp_settings_sections[$page] ) ){
        return;
      }
      
      $tabs = '<h2 class="nav-tab-wrapper adb-nav-tab-wrapper">';
      $content = '';
      
      $i=0;
      
      $selected_tab = isset($_GET['setting_tab']) && $_GET['setting_tab'] ? sanitize_key($_GET['setting_tab']) : '' ;  
 
      foreach ( (array) $wp_settings_sections[$page] as $section ) {
        
        //print_r($section);
        
        $class_active = '';
        
        if ( $section['title'] ){
            
            $class_active = (($selected_tab && $section['id'] == $selected_tab) || (!$selected_tab && !$i)) ? 'nav-tab-active' : '';
            
            $tabs .= '<a class="nav-tab '.$class_active.'" href="#'.$section['id'].'" data-target="'.$section['id'].'">'.$section['title'].'</a>
            ';
            
            $i++;
        }    
            
        if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ){
            continue;
        }
        
        $class_content_active = $class_active ? 'tab-target-active' : '';
        
        ob_start();
        
        echo '<div id="'.$section['id'].'" class="tab-target '.$class_content_active.'">';
        
        if ( $section['callback'] ){
            call_user_func( $section['callback'], $section );
        }
            
        echo '<table class="form-table">';
        do_settings_fields( $page, $section['id'] );
        echo '</table>';
        echo '</div>';
        
        $content .= ob_get_clean();
        
     }
     
     $tabs .= '</h2>';
     
     echo $tabs.$content;
     
     return;
   }     

////////////////////////////////////////////////
    /**
     * Register and add settings
     * 
     * @return
     */
    public static function settings_page_init(){
        
        register_setting(
            ADB_Settings::$option_menu_slug, // Option group
            ADB_Settings::$option_name, // Option name
            array( __CLASS__, 'sanitize_settings' ) // Sanitize
        );

        ///////// General

        add_settings_section(
            'setting_section_general', // ID
            __('General',ADB_TEXTDOMAIN), // Title
            array( __CLASS__, 'print_section_general' ), // Callback
            ADB_Settings::$option_menu_slug // Page
        );
        
        add_settings_field(
            'classic_editor', // ID
            __('Use classic editor for Ad Block post type',ADB_TEXTDOMAIN), // Title
            array( __CLASS__, 'is_active_callback' ), // Callback
            ADB_Settings::$option_menu_slug, // Page
            'setting_section_general', // Section
            array('option' => 'classic_editor', 'settings_name' => ADB_Settings::$option_name) // Args array
        );
        
        add_settings_field(
            'date_format', // ID
            __('Date format',ADB_TEXTDOMAIN), // Title
            array( __CLASS__, 'setting_date_format' ), // Callback
            ADB_Settings::$option_menu_slug, // Page
            'setting_section_general' // Section
        );
        
        ////////////////
        
        do_action('adb_settings_after_general_fields', ADB_Settings::$option_menu_slug, ADB_Settings::$option_name);
        
        return;
        
    }    

////////////////////////////////

     /**
      * Sanitize each setting field as needed
      *
      * @param array $input Contains all settings fields as array keys
      * @return array
      */
      static function sanitize_settings( $input ){
    
        $new_input = array();
        
        $new_input['date_format'] = $input['date_format'] == 'd/m/Y' || $input['date_format'] == 'm/d/Y' ? $input['date_format'] : 'd/m/Y';
        
        $new_input['classic_editor'] = !$input['classic_editor'] ? 0 : 1;

        $new_input = apply_filters('adb_sanitize_'.ADB_Settings::$option_name, $new_input, $input);

        return $new_input;
}

////////////////////////////

/**
* Print the Section text
*/
    public static function print_section_general(){
        // echo '';
    }
    
//////////////////////////////////////    

    public static function color_field_callback($args){
        printf(
            '<input type="text" id="'.$args['option'].'" class="color-field" name="'.$args['settings_name'].'['.$args['option'].']" value="%s" />',
            isset( ADB_Settings::$settings[$args['option']] ) ? esc_attr( ADB_Settings::$settings[$args['option']]) : $args['color']
        );
}

////////////////////////////////////
    
    public static function text_field_callback($args){
        $add_class = isset($args['translate']) ? ' class="q_translatable"' : '';
        
        printf(
            '<input type="text"'.$add_class.' id="'.$args['option'].'" name="'.$args['settings_name'].'['.$args['option'].']" value="%s" />',
            isset( ADB_Settings::$settings[$args['option']] ) ? esc_attr( ADB_Settings::$settings[$args['option']]) : ''
        );
    }

/////////////////////////////////////////
    
    public static function textarea_callback($args){
        printf(
            '<textarea id="'.$args['option'].'" class="q_translatable" name="'.$args['settings_name'].'['.$args['option'].']" rows=5>%s</textarea>',
            isset( ADB_Settings::$settings[$args['option']] ) ? esc_attr( ADB_Settings::$settings[$args['option']]) : ''
        );
}    

/////////////////////////////////////

    public static function img_field_callback($args){
       $img_src = isset( ADB_Settings::$settings[$args['option']] ) ? ADB_Settings::$settings[$args['option']] : '';

        echo '<div id="'.$args['option'].'_upload_block"><img id="'.$args['option'].'_preview" src="'.$img_src.'" class="_img_field_preview"/>';
        echo '<input type="text" id="'.$args['option'].'" name="'.$args['settings_name'].'['.$args['option'].']" value="'.$img_src.'" /><input type="button" class="'.$args['option'].'_upload" value="'.__('Upload', ADB_TEXTDOMAIN).'"></div>';

        echo '<script>
    jQuery(document).ready(function($) {
        $(\'.'.$args['option'].'_upload\').click(function(e) {
            e.preventDefault();

            var custom_uploader = wp.media({
                title: \''.__('Custom Image', ADB_TEXTDOMAIN).'\',
                button: {
                    text: \''.__('Upload Image', ADB_TEXTDOMAIN).'\'
                },
                multiple: false  // Set this to true to allow multiple files to be selected
            })
            .on(\'select\', function() {
                var attachment = custom_uploader.state().get(\'selection\').first().toJSON();
                $(\'#'.$args['option'].'_preview\').attr(\'src\', attachment.url);
                $(\'#'.$args['option'].'\').val(attachment.url);

            })
            .open();
        });
    });
</script>';
}

////////////setting_page_select 

   public static function setting_page_select($args){
    
    //$args['settings_name']

        $selected_page = isset(ADB_Settings::$settings[$args['option']]) ?  ADB_Settings::$settings[$args['option']] : 0;
        
        $args2 = array(
        'post_type'   => 'page',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        );
        
        $posts = get_posts( $args2 );
        
        $post_options = '';
        if ( $posts ) {
          foreach ( $posts as $post ) {
             $post_options .= '<option value="'. $post->ID .'" '. selected( $selected_page, $post->ID, false ) .'>'. $post->post_title .'</option>';
          }
        }
        
        $post_options = $post_options ? '<select id="'.$args['option'].'" name="'.$args['settings_name'].'['.$args['option'].']">
        '.$post_options.'
        </select>' : '';

           echo $post_options;

    }
    
///////////////////////////////////////////////
    
    public static function is_active_callback($args){
        
        $check = isset(ADB_Settings::$settings[$args['option']]) ?  ADB_Settings::$settings[$args['option']] : 0;

        $checked1 = $check ? 'checked' : '';
        $checked2 = !$check ? 'checked' : '';

        echo '<p><input id="'.$args['settings_name'].'['.$args['option'].']1" name="'.$args['settings_name'].'['.$args['option'].']" type="radio" value="1" '.$checked1.'/><label for="'.$args['settings_name'].'['.$args['option'].']1">'.__('Yes', ADB_TEXTDOMAIN).'</label></p>';
       echo '<p><input id="'.$args['settings_name'].'['.$args['option'].']2" name="'.$args['settings_name'].'['.$args['option'].']" type="radio" value="0" '.$checked2.'/><label for="'.$args['settings_name'].'['.$args['option'].']2">'.__('No', ADB_TEXTDOMAIN).'</label></p>';
       
    }

/////////////////////

    public static function setting_date_format(){

        $check = isset(ADB_Settings::$settings['date_format']) ?  ADB_Settings::$settings['date_format'] : 'd/m/Y';

        $checked1 = $check == 'd/m/Y' ? 'checked' : '';
        $checked2 = $check == 'm/d/Y' ? 'checked' : '';

        echo '<p><input id="'.ADB_Settings::$option_name.'[date_format]1" name="'.ADB_Settings::$option_name.'[date_format]" type="radio" value="d/m/Y" '.$checked1.'/><label id="'.ADB_Settings::$option_name.'_date_format1" for="'.ADB_Settings::$option_name.'[date_format]1">'.__('d/m/Y', ADB_TEXTDOMAIN).'</label></p>';
        echo '<p><input id="'.ADB_Settings::$option_name.'[date_format]2" name="'.ADB_Settings::$option_name.'[date_format]" type="radio" value="m/d/Y" '.$checked2.'/><label id="'.ADB_Settings::$option_name.'_date_format2" for="'.ADB_Settings::$option_name.'[date_format]2">'.__('m/d/Y', ADB_TEXTDOMAIN).'</label></p>';       

    }

///////////////////////////
  
}

ADB_Settings_admin::init();
