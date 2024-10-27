<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Post_types Class.
 * 
 * @class 		ADB_Post_types
 * @version		1.0.1
 * @author 		Sergey Kravchenko
 */

class ADB_Post_types {
    
    // variables to use
    static $nonce_title = 'adb-nonce';
    
    public static $adblock_post_type = 'adblock';
    
    static $shortcode_adblock_name = 'adblock';
    
    // cache
    private static $adblock_content = array();
    
    private static $adblock_auto_content = array();
    
    private static $adblock_auto_content_check = 0;
    
//////////////////////////////
    /**
	 * Hook in tabs.
	 */
    public static function init() {
		
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 0 );
        
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ), 10 );
        
        add_shortcode( self::$shortcode_adblock_name, array( __CLASS__, 'shortcode_adblock' ), 10 );
        
        add_filter('template_redirect', array( __CLASS__, 'get_auto_add_adblocks'), 100);
        
        add_filter('the_content', array( __CLASS__, 'add_auto_content'), 100);
          
	}          
    
//////////////////////////////
    /**
	 * Register post types.
	 */
    public static function register_post_types() {
        
    $labels = array(
		'name'                => _x( 'Ad Block', 'Post Type General Name', ADB_TEXTDOMAIN ),
		'singular_name'       => _x( 'Ad Block', 'Post Type Singular Name', ADB_TEXTDOMAIN ),
        'menu_name'          => _x( 'Ad Blocks', 'admin menu', ADB_TEXTDOMAIN ),
		'name_admin_bar'     => _x( 'Ad Blocks', 'add new on admin bar', ADB_TEXTDOMAIN ),
		'add_new'            => __( 'Add new', ADB_TEXTDOMAIN ),
		'add_new_item'        => __( 'Add New Ad Block', ADB_TEXTDOMAIN ),
        'new_item'           => __( 'New Ad Block', ADB_TEXTDOMAIN ),
		'edit_item'          => __( 'Edit Ad Block', ADB_TEXTDOMAIN ),
		'view_item'          => __( 'View Ad Block', ADB_TEXTDOMAIN ),
		'all_items'          => __( 'Ad Blocks', ADB_TEXTDOMAIN ),
		'search_items'       => __( 'Search Ad Block', ADB_TEXTDOMAIN ),
		'parent_item_colon'  => __( 'Parent Ad Block:', ADB_TEXTDOMAIN ),
		'not_found'          => __( 'Not found.', ADB_TEXTDOMAIN ),
		'not_found_in_trash' => __( 'Not found in Trash.', ADB_TEXTDOMAIN ),
		'update_item'         => __( 'Update Ad Block', ADB_TEXTDOMAIN ),
	);    

// Set other options for Custom Post Type

	$args = array(
		'description'         => __( 'Ad Blocks', ADB_TEXTDOMAIN ),
		'labels'              => $labels,
		// Features this CPT supports
		'supports'            => array( 'title', 'editor' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
        'menu_position'        => 25,
        'menu_icon' => 'dashicons-media-code',
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
	);
    
    if (!ADB_Settings::get_option('classic_editor')){
        $args['show_in_rest'] = true;
    }

	// Registering your Custom Post Type
	register_post_type( self::$adblock_post_type, $args );
    
    return;
    
    }
    
//////////////////////////////
    /**
	 * Enqueue assets.
	 */
    public static function wp_enqueue_scripts() {
        
        wp_enqueue_style( 'adblocks-style', plugins_url( "css/adblocks.css", ADB_PLUGIN ));
        
        return; 
      
     }    
    
////////////////////////////
    /**
	* 
    * Get adblock html by name(s)
    * 
	* @param string $names - comma separated list
	*
	* @return string
	*/
	public static function get_adblock_content( $names ) {
	   
        $output = '';
            
        $names_arr = explode(',', $names);
            
        $names_arr = array_map('sanitize_title', $names_arr);
            
        foreach ($names_arr as $name){
                
            if (isset(self::$adblock_content[$name])){
                
               $output .= self::$adblock_content[$name];
                
            } else {
                
               $adblock_post_content = self::get_adblock_post_content($name);
               
               if ($adblock_post_content){
                
                  self::$adblock_content[$name] = '
                 <div class="'.self::$shortcode_adblock_name.'-holder" data-name="'.$name.'">'.$adblock_post_content.'</div>
                 ';
                   
                  $output .= self::$adblock_content[$name];
                
               } else {
                        
                  self::$adblock_content[$name] = '';
                        
               }
            }
            
        }    
			
		return $output;
	}
    
////////////////////////////
    /**
	* 
    * Get adblock post content by name
    * 
	* @param string $name
	*
	* @return string
	*/
	public static function get_adblock_post_content( $name ) {
	   
        $output = '';
            
        $post_args = array(
			'post_type'   => self::$adblock_post_type,
            'numberposts' => 1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                  'key' => 'shortcode_key_name',
                  'value' => $name,
                  'compare' => '=',
                )
              )
            );
                
        $posts = get_posts( $post_args );
                
        if ( $posts ) {
                    
            $post_id = $posts[0]->ID;
                    
            //// check schedule rules
            $is_active = apply_filters('adb_'.self::$shortcode_adblock_name.'_is_active_block', true, $posts[0], $name);
                    
            if ($is_active){
                    
                $output = apply_filters('the_content', $posts[0]->post_content);
                   
                $output = apply_filters('adb_'.self::$shortcode_adblock_name.'_content_html', $output, $name);
                    
            }
        }    
			
		return $output;
	}    
    
//////////////////////////////
        /**
		 * 
		 * @param array $atts
		 * @param string $content
		 *
		 * @return string
		 */
		public static function shortcode_adblock( $atts, $content = null ) {
			
			$output = '';
            
            $args = shortcode_atts( array(
				'name'      => '',
			), $atts, self::$shortcode_adblock_name );
            
            $args['name'] = sanitize_text_field($args['name']);
            
            if ($args['name']){
                
                $output = self::get_adblock_content($args['name']);
                
                $output = $output ? '
                 <div class="'.self::$shortcode_adblock_name.'-container">'.$output.'</div>
                ' : '';
            
            }
			
			return $output;
		}    
    
////////////////////////////
    /**
	 * Get terms array.
     * 
     * @param string $taxonomy
     * 
     * @return array
	 */
    public static function get_terms_options($taxonomy) {
        
        $output = array();
        
        $terms = get_terms( array(
          'taxonomy' => $taxonomy,
          'hide_empty' => false
        ) );
        
        if ( !empty($terms) ){
            foreach( $terms as $tax_term ) {
               $output[$tax_term->term_id] = apply_filters('translate_text', $tax_term->name);  
            }
        }
        
        return $output;       
    }
    
//////////////////////////////
    /**
	 * Get posts option list.
     * 
     * @param string $post_type
     * @param array $ids
     * 
     * @return array
	 */
    public static function get_posts_options($post_type, $ids = array()) {
    $args = array(
        'post_type'   => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    if (!empty($ids)){
        $args['post__in'] = $ids;
    }
    
    $posts = get_posts( $args );
    $post_options = array();
    if ( $posts ) {
        foreach ( $posts as $post ) {  
          $post_options[ $post->ID ] = $post->post_title;
        }
    }
    return $post_options;
    
    }
    
////////////////////////////
    /**
	* 
    * Get auto add adblocks content
	*
	* @return
	*/
	public static function get_auto_add_adblocks() {
	   
        global $post;
        
        if (!is_admin() && is_singular()){
            
            $post_args = array(
				'post_type'   => self::$adblock_post_type,
                'numberposts' => -1,
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
                'meta_query' => array(
                  array(
                    'key' => 'add_to_posts',
                    'value' => 1,
                    'compare' => '=',
                  )
                 )
            );
            
            $adblock_posts = get_posts( $post_args );
            
            self::$adblock_auto_content = array();
            
            self::$adblock_auto_content_check = 0;
            
            if ( $adblock_posts ) {
                
                foreach($adblock_posts as $adblock_post){
                    
                    $add_post_types = get_post_meta($adblock_post->ID, 'add_post_types', 1);
                    
                    if (is_array($add_post_types) && !empty($add_post_types)){
                        
                        $add_post_types = array_flip($add_post_types);
                        
                        $is_target = apply_filters('adb_'.self::$shortcode_adblock_name.'_is_target_post', isset($add_post_types[$post->post_type]), $post, $add_post_types, $adblock_post);
                        
                        if ($is_target){
                            
                            $shortcode_key_name = get_post_meta($adblock_post->ID, 'shortcode_key_name', 1);
                            
                            $after_content = absint(get_post_meta($adblock_post->ID, 'add_location', 1));
                            
                            self::$adblock_auto_content[$shortcode_key_name] = array(
                                   'content' => self::get_adblock_content($shortcode_key_name),
                                   'after_content' => $after_content,
                            );
                            
                            self::$adblock_auto_content_check = $post->ID;
                            
                        }
                        
                    }
                    
                }
                
            }  /// end if $adblock_posts
            
        }    
			
		return;
	}
    
////////////////////////////
    /**
	* 
    * Auto add adblocks content
    * 
	* @param string $content 
	*
	* @return
	*/
	public static function add_auto_content($content) {
	   
        global $post;
        
        if (!is_admin() && is_singular() && self::$adblock_auto_content_check == $post->ID){
            
            foreach(self::$adblock_auto_content as $shortcode_key_name => $adblock_auto_content){
                
                $content = $adblock_auto_content['after_content'] ? $content.$adblock_auto_content['content'] : $adblock_auto_content['content'].$content;
                
            }
            
        }    
			
		return $content;
	}                     
            
////////////////////////////
    
}

ADB_Post_types::init();
