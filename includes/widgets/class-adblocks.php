<?php

class ADB_Widget_adblocks extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'adb_widget_adblocks',
// Widget name will appear in UI
__('Ad Blocks', ADB_TEXTDOMAIN),
// Widget description
array( 'description' => __('Show Ad Blocks', ADB_TEXTDOMAIN), )
);
}

// Creating widget front-end
// This is where the action happens
function widget( $args, $instance ) {
  Global $post;
  
  extract( $args );
  $title = apply_filters('widget_title', $instance['title']);
      echo $before_widget;
      if ( $title ) {
        echo $before_title . $title . $after_title;
      }
      
     echo do_shortcode('['.ADB_Post_types::$shortcode_adblock_name.' name="'.$instance['shortcode_key_name'].'"]');

     echo $after_widget;  
}

////////////////////////////////
// Updating widget
function update( $new_instance, $old_instance ) {
  $instance = $old_instance;
  $instance['title'] = strip_tags($new_instance['title']);
  $instance['shortcode_key_name'] = sanitize_text_field($new_instance['shortcode_key_name']);
  return $instance;
}

/////////////////////
// Widget Backend
 function form( $instance ) {

    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
    $shortcode_key_name = isset($instance['shortcode_key_name']) ? esc_attr($instance['shortcode_key_name']) : '';

        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ADB_TEXTDOMAIN); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        
        <p>
          <label for="<?php echo $this->get_field_id('shortcode_key_name'); ?>"><?php _e('Ad block name(s):', ADB_TEXTDOMAIN); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('shortcode_key_name'); ?>" name="<?php echo $this->get_field_name('shortcode_key_name'); ?>" type="text" value="<?php echo $shortcode_key_name; ?>" />
        </p>
<?php
}

} // Class ends here

// Register and load the widget
function adb_load_widget_adblocks() {
	register_widget( 'ADB_Widget_adblocks' );
}
add_action( 'widgets_init', 'adb_load_widget_adblocks' );
