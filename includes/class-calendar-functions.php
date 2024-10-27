<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ADB_Calendar_functions Class.
 * 
 * @class 		ADB_Calendar_functions
 * @version		1.0.1
 * @author 		Sergey Kravchenko
 */

class ADB_Calendar_functions {

    // sunday number
    static $week_sunday = 0;
    
    // first day number on the calendar week line
    static $week_first_day = 0;
    
    // last day number on the calendar week line
    static $week_last_day = 6;
    
    static $timezone = '';
    
///////////////////////////////////////    
    /**
	 * Setup vars based on start of week 
	 */
    public static function init() {
        
        if(get_option('start_of_week') != 0){
           self::$week_first_day = 1;
           self::$week_sunday = 7;
           self::$week_last_day = 7;
        }
        
        if (!self::$timezone){
        
          $timezone = get_option('timezone_string');
        
          $wp_offset = get_option('gmt_offset');
        
          if (!$timezone && $wp_offset){
            
            $sign = $wp_offset > 0 ? '+' : '-';
            $min = 60*abs($wp_offset);
            
            $h = floor($min/60);
            $h = $h < 10 ? '0'.$h : $h;
            
            $m = $min%60;
            $m = $m < 10 ? '0'.$m : $m;
            
            $timezone = $sign.$h.':'.$m;
            
          } elseif (!$timezone) {
            $timezone = 'UTC';
          }
          
          self::$timezone = $timezone;
        
        }
               
	}
    
///////////////////////////////////////
    /**
	 * Get week days short names.
     * 
     * @return array
	 */
    public static function get_week_days_arr(){ 
    
    return !self::$week_sunday ? array(
      0 => __('Sun', ADB_TEXTDOMAIN ),
      1 => __('Mon', ADB_TEXTDOMAIN ),
      2 => __('Tue', ADB_TEXTDOMAIN ),
      3 => __('Wed', ADB_TEXTDOMAIN ),
      4 => __('Thu', ADB_TEXTDOMAIN ),
      5 => __('Fri', ADB_TEXTDOMAIN ),
      6 => __('Sat', ADB_TEXTDOMAIN ),   
     ) : array(
      1 => __('Mon', ADB_TEXTDOMAIN ),
      2 => __('Tue', ADB_TEXTDOMAIN ),
      3 => __('Wed', ADB_TEXTDOMAIN ),
      4 => __('Thu', ADB_TEXTDOMAIN ),
      5 => __('Fri', ADB_TEXTDOMAIN ),
      6 => __('Sat', ADB_TEXTDOMAIN ),
      7 => __('Sun', ADB_TEXTDOMAIN ),   
     );     
    }
    
///////////////////////////////////////
    /**
	 * Get week days short names.
     * 
     * @return array
	 */
    public static function get_week_days_arr_2(){ 
    
    return !self::$week_sunday ? array(
      0 => __('Su', ADB_TEXTDOMAIN ),
      1 => __('Mo', ADB_TEXTDOMAIN ),
      2 => __('Tu', ADB_TEXTDOMAIN ),
      3 => __('We', ADB_TEXTDOMAIN ),
      4 => __('Th', ADB_TEXTDOMAIN ),
      5 => __('Fr', ADB_TEXTDOMAIN ),
      6 => __('Sa', ADB_TEXTDOMAIN ),   
     ) : array(
      1 => __('Mo', ADB_TEXTDOMAIN ),
      2 => __('Tu', ADB_TEXTDOMAIN ),
      3 => __('We', ADB_TEXTDOMAIN ),
      4 => __('Th', ADB_TEXTDOMAIN ),
      5 => __('Fr', ADB_TEXTDOMAIN ),
      6 => __('Sa', ADB_TEXTDOMAIN ),
      7 => __('Su', ADB_TEXTDOMAIN ),   
     );     
    }    

///////////////////////////////////////
    /**
	 * Get week day short name by its number.
     * 
     * @param int $i - week day number.
     * @return array
	 */
    public static function get_week_day($i){
      $arr = self::get_week_days_arr();
      if ($i > 7 || $i < 0) $i = 1;
      $i = $i%7;
      if (self::$week_sunday && $i==0){
            $i = 7; 
      }
       
      return $arr[$i];
    }

///////////////////////////////////////
    /**
	 * Get week days short names list.
     * 
     * @return array
	 */
    public static function cmb2_get_week_options(){
        
        return self::get_week_days_arr();
            
    }

///////////////////////////////////////
    /**
	 * Get week day number.
     * 
     * @param Date Object (PHP).
     * @return int
	 */
    public static function get_week_day_num($date_obj){
        
        $day_num = !self::$week_sunday ? $date_obj->format("w") : $date_obj->format("N");
        return $day_num;
        
    }

///////////////////////////////////////
    /**
	 * Get next week day number.
     * 
     * @param int - current day number.
     * @return int
	 */
    public static function get_next_week_day_num($w){
        
        $w = ($w+1)%7;
        if (self::$week_sunday && $w==0){
            $w = 7; 
        }
        return $w;
        
    }

///////////////////////////////////////
    /**
	 * Is the day number - last day number on the calendar week line?
     * 
     * @param int - day number.
     * @return boolean
	 */
    public static function is_week_day_weekend($w){
        
        return (self::$week_last_day == $w) ? true : false;
         
    }
    
///////////////////////////////////////
    /**
	 * Is date valid
     * 
     * @param string $date.
     * @param string $format - PHP date format
     * @return boolean
	 */
    public static function isValidDate($date, $format = 'Y-m-d'){
        
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date; 
        
    }

///////////////////////
     /**
	 * Is time valid
     * 
     * @param string $time
     * @param string $format - PHP time format
     * @return boolean
	 */
    public static function isValidTime($time, $format = ''){
        
        $format = $format ? 'Y-m-d '.$format : 'Y-m-d '.get_option('time_format');
        $date = '2019-01-01 '.$time;
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
        
    }
    
////////////////////////
     /**
	 * Convert d/m/Y or m/d/Y to SQL format
     * @param string $date
     * @return string
	 */
     public static function date_to_sql($date){
        
        $d = DateTime::createFromFormat(ADB_Settings::$settings['date_format'], $date);
        return $d ? $d->format('Y-m-d') : '';
        
     }
     
////////////////////////
     /**
	 * Convert SQL date format to d/m/Y or m/d/Y
     * @param string $date
     * @return string
	 */
     public static function date_from_sql($date){
        
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d ? $d->format(ADB_Settings::$settings['date_format']) : '';
        
     }
     
//////////////////////////////////////
     /**
	 * Get now DateTime obj with current timezone
     * 
     * @return obj
	 */ 
     public static function datetime_local(){
        
        $date_now_obj = new DateTime('', new DateTimeZone(self::$timezone));
        
        return $date_now_obj;
      
     }               

/////////////////////////////    
    
}

ADB_Calendar_functions::init();
