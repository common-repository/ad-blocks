<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Functions Class.
 * Get general settings
 * @class 		ADB_Functions
 * @version		1.0.0
 * @author 		Sergey Kravchenko
 */

class ADB_Functions {
    
//////////////////////////////////////
     /**
	 * Get range select options.
     * 
     * @param int $start
     * @param int $end
     * @param int $step
     * @param int $value
     * @param boolean $lead_zero
     * 
     * @return string
	 */ 
     public static function get_range_select_options($start, $end, $step = 1, $value = false, $lead_zero = false){
     
        $output = '';
      
        for($i = $start; $i <= $end; $i += $step){
           
           $n = $lead_zero && $i <=9 ? '0'.$i : $i; 
            
           $output .= '<option value="'. $n .'" '. selected( $value, $n, false ) .'>'. $n .'</option>';
        }
      
        return $output;
      
     }     
     
//////////////////////////////////////
     /**
	 * Compare dates for usort() ASC
     * @param string $date_1 - format Y-m-d H:i
     * @param string $date_2 - format Y-m-d H:i
     * @return int
	 */ 
     public static function compare_sql_dates_asc($date_1, $date_2){
     
        $ad = new DateTime($date_1);
        $bd = new DateTime($date_2);
        
        if ($ad == $bd) {
            return 0;
        }
        return $ad < $bd ? -1 : 1;
      
     }
     
//////////////////////////////////////
     /**
	 * Compare dates for usort() DESC
     * @param string $date_1 - format Y-m-d H:i
     * @param string $date_2 - format Y-m-d H:i
     * @return int
	 */ 
     public static function compare_sql_dates_desc($date_1, $date_2){
     
        $ad = new DateTime($date_1);
        $bd = new DateTime($date_2);
        
        if ($ad == $bd) {
            return 0;
        }
        return $ad > $bd ? -1 : 1;
      
     }
     
///////////////////////////////////////
    /**
	 * Get page content.
     * @param int $post_id
     * @return string
	 */
    public static function get_page_content($post_id) {
        
        $post = get_post($post_id);
        $output = !empty($post) ? apply_filters('the_content', $post->post_content) : '';
        
        return $output;
    }
    
///////////////////////////////////////
}
