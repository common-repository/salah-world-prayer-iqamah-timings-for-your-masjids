<?php

/*
Plugin Name: Salah World BETA
Version: 1.0
Plugin URI: https://wordpress.org/plugins/salah-world-prayer-iqamah-timings-for-your-masjids/
Description: Display prayer, iqamah timings, and event with shortcodes. Display notification if iqamah timings will change. 
Author: Sahaab Ibn Zaid
Author URI: http://salahworld.org
*/

/*


/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/.

*/

if(!class_exists('fluid_notification_bar') && !class_exists('Fn_notibar_options')){

    class fluid_notification_bar{
 function localize_plugin($page) {
			load_plugin_textdomain(AEC_NAME, false, AEC_NAME . '/locale/');
			$timezone = get_option('timezone_string');
			if ($timezone) {
				date_default_timezone_set($timezone);
			} else {
				// TODO: look into converting gmt_offset into timezone_string
				date_default_timezone_set('UTC');
			}
			
			// localization: date/time
			if (get_option('timezone_string')) {
				$this->timezone = get_option('timezone_string');
			} 
			else {
				function timezone__error() {
				$tz_class = 'notice notice-error';
				$tz_message = __( '<p>Salah World requires a city value for the Timezone setting.<br> Please update your blog settings <a href="' . admin_url() . 'options-general.php">here.</a>', 'sample-text-domain' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', $tz_class, $tz_message ); 
				}
		
				add_action( 'admin_notices', 'timezone__error' );
			}
			
			if (get_option('prayer_long')) {
				$this->timezone = get_option('prayer_long');
			} 
			else {
				function long__error() {
				$long_class = 'notice notice-warning';
				$long_message = __( '<p>Please complete the settings for Salah World <a href="' . admin_url() . 'admin.php?page=setting">here.</a>', 'sample-text-domain' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', $long_class, $long_message ); 
				}
		
				add_action( 'admin_notices', 'long__error' );
			}
			
 }

        public function __construct(){
add_action('init', array($this, 'localize_plugin'));

require_once(plugin_dir_path(__FILE__) . "dw-promobar.php");

           require_once(plugin_dir_path(__FILE__) . "functions.php");
            //Initialize settings page
            require_once(plugin_dir_path(__FILE__) . "fnbar_options.php");
            $fn_notibar_options = new fn_notibar_options;

            
function salah_world_install() {


    global $wpdb;



    $table_name = $wpdb->prefix . 'iqamahTimes';



    $sql = "CREATE TABLE $table_name (

      id int(11) NOT NULL AUTO_INCREMENT,
      `field_1` date NOT NULL,
      `fajr` tinytext NOT NULL,
      `zuhr` tinytext NOT NULL,
      `asr` tinytext NOT NULL,
      `magrib` tinytext NOT NULL,
      `isha` tinytext NOT NULL,

      UNIQUE KEY id (id)

    );";



    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );
	
	

}

// run the install scripts upon plugin activation

register_activation_hook(__FILE__,'salah_world_install');
           
        }



    }

}

$fluid_notification_bar = new fluid_notification_bar;


?>