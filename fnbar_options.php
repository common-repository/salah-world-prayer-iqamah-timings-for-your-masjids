<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class fn_notibar_options {

    //Holds the values to be used in the fields callbacks
    private $options;

    public function __construct(){
		
		//Delete Row
		add_action( 'wp_ajax_delete_row', array($this,'delete_row') );
		
		//New Row
		add_action( 'wp_ajax_new_row', array($this,'new_row') );	
		
		//Update Row
		add_action( 'wp_ajax_update_row', array($this,'update_row') );	
		
        add_action("admin_menu", array($this,"add_plugin_menu_fnbar"));
        add_action("admin_init", array($this,"register_dasettings"));

        add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));

add_filter('widget_text', 'do_shortcode');
  	}

    public function add_plugin_menu_fnbar() {

		$page_title = 'Welcome to Sahaab World';
		$menu_title = 'Salah World BETA';
		$capability = 'manage_options';
		$menu_slug  = 'salah-world';
		$function   =  array($this, "create_admin_page_fnbar");
		$icon_url   = 'dashicons-admin-site';
		$function1   =  array($this, "create_admin_page_boo");
		$function2   =  array($this, "create_admin_page_poo");
		
		add_menu_page( $page_title,
					 $menu_title,
					 $capability,
					 $menu_slug,
					 $function,
					 $icon_url );   
		add_submenu_page( 'salah-world', 
						  'Iqamah Times',
						  'Iqamah Times', 
						  'manage_options', 
						  'salah-world' 
						); 
		add_submenu_page( 'salah-world',
						  'Settings',
						  'Settings', 
						  'manage_options',
						  'setting', 
						  $function1
						);   
		add_submenu_page( 'salah-world',
						  'Notification Bar ',
						  'Notification Bar',
						  'administrator', 
						  'dw_promobar',
						  'dwpb_settings_page'
						); 
		add_submenu_page( 'options-general.php',
						  'DW PromoBar', 
						  'DW PromoBar',
						  'manage_options', 
						  'dw_promobar',
						  'dwpb_settings_page'
						); 
		add_submenu_page( 'salah-world',
						  'About',
						  'About',
						  'manage_options', 
						  'about',
						  $function2
						);  
    }


	function delete_row() {
		$id = intval($_REQUEST['id']);
		$id = sanitize_text_field($id);
		if ( ! $id ) {
			$id = '';
		}
		
		if (is_numeric($id)){
			global $wpdb;
			$table_name = $wpdb->prefix . 'iqamahTimes';
			$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d",$id));
		}
}

	function new_row() {
		$value=$_REQUEST["value"];
		$fajr=$_REQUEST["fajr"];
		$zuhr=$_REQUEST["zuhr"];
		$asr=$_REQUEST["asr"];
		$magrib=$_REQUEST["magrib"];
		$isha=$_REQUEST["isha"];
		
		if (!preg_match("/\d\d\d\d-\d\d-\d\d/", $value )) 
			$value = '';
		
		if(!preg_match("/\d{1,2}:*\d{0,2}/", $fajr) )
			$fajr = '';
		if(!preg_match("/\d{1,2}:*\d{0,2}/", $zuhr))
			$zuhr = '';
		if(!preg_match("/\d{1,2}:*\d{0,2}/", $asr))
			$asr = '';
		if(!preg_match("/\d{1,2}:*\d{0,2}/", $magrib))
			$magrib = '';
		if(!preg_match("/\d{1,2}:*\d{0,2}/", $isha))
			$isha = '';	
		
		$value = sanitize_text_field($value);
		$fajr = sanitize_text_field($fajr);
		$zuhr = sanitize_text_field($zuhr);
		$asr = sanitize_text_field($asr);
		$magrib = sanitize_text_field($magrib);
		$isha = sanitize_text_field($isha);		
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'iqamahTimes';
		
		$wpdb->query( $wpdb->prepare( 
	"
		INSERT INTO $table_name
		( field_1, fajr, zuhr, asr, magrib, isha )
		VALUES ( %s, %s, %s, %s, %s, %s )
	", 
        array(
		$value, 
		$fajr, 
		$zuhr,
		$asr,
		$magrib,
		$isha
	) 
) );
		
		
		die();
	}

	function update_row() {
		
		$name=$_REQUEST["name"];
		$value=$_REQUEST["value"];

		$id = intval($_REQUEST['id']);
		$id = sanitize_text_field($id);
		
		if ( ! $id ) {
			$id = '';
		}
			
		if(!( $name == "field_1" || $name == "fajr" || $name == "zuhr" || $name == "asr" || $name == "magrib" || $name == "isha"))
			$name = "";
		
		if(!(preg_match("/\d{1,2}:*\d{0,2}/", $value) || !preg_match("/\d\d\d\d-\d\d-\d\d/", $value )))
			$value = "";
		
		if (is_numeric($id)){
		global $wpdb;
		$table_name = $wpdb->prefix . 'iqamahTimes';

	$wpdb->query( $wpdb->prepare(
	"UPDATE $table_name 
	Set $name = %s
	WHERE id = %d
	", 
        array(
		$value,
		$id
	) 
));	


		
		}
	}	

	function register_dasettings() {

		$prayer_settings_array = array(
			//General Setting
			'prayer_method',
			'prayer_asr',
			'prayer_lat',
			'prayer_long',
			'prayer_country',
			'prayer_time_zone',
			'prayer_khutbah1',
			'prayer_khutbah2',
			'prayer_khutbah3',
			'prayer_iqamah1',
			'prayer_iqamah2',
			'prayer_iqamah3',
			'sw_current_color',
			'sw_hover_color',
			'sw_day_color',
			'sw_title_color',
			'sw_sticky_color',
			'sw_month_size'
		);
		foreach ($prayer_settings_array as $value) {
			register_setting( 'prayer-settings-group', $value );
		}
	}

public function create_admin_page_boo (){



        $this->options = get_option ( 'fluid_notification_bar_settings' );
      
        ?>
            <div class="wrap">

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">


                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <div class="postbox">
                                    <h3><span class="dashicons dashicons-admin-generic"></span>Prayer Time Settings</h3>
                                    <div class="inside">
                                 





<div class="wrap">

<form method="post" action="options.php">
    <?php settings_fields( 'prayer-settings-group' ); ?>
    <?php do_settings_sections( 'prayer-settings-group' ); ?>
	
	<?php //do_action( 'dwpb_previvew' ); ?>
    <div id="dwpb-steps">
		
	
	
		<table class="form-table">
			<tr valign="top">



				<th scope="row"><?php _e('Asr Calculation Method','dwpb') ?></th>
				<td>
					<?php 
$acms = array(
    '1' => 'Hanafi',
    '0' => 'Standard'
);
?>
<select name="prayer_asr">
<?php foreach( $acms as $var => $acm): ?>
<option value="<?php echo $var ?>"<?php if( $var == get_option('prayer_asr') ): ?> selected="selected"<?php endif; ?>><?php echo $acm?></option>
<?php endforeach; ?>
</select>


				</td></tr><tr>
				<th scope="row"><?php _e('Prayer Time Calculation Method','dwpb') ?></th>
                               <td>
					<?php 
$acms = array(
    '2' => 'ISNA - Islamic Society of North America',
    '5' => 'Egyptian General Authority of Survey',
    '4' => 'Umm Al Qura',
    '3' => 'Muslim World League',
    '1' => 'University of Islamic Sciences, Karachi'
);
?>
<select name="prayer_method">
<?php foreach( $acms as $var => $acm): ?>
<option value="<?php echo $var ?>"<?php if( $var == get_option('prayer_method') ): ?> selected="selected"<?php endif; ?>><?php echo $acm?></option>
<?php endforeach; ?>
</select>


				</td></tr><tr><th scope="row"><span class="dashicons dashicons-location-alt"></span> Location Settings</tr></th>
							
</tr><tr>

			
<th scope="row"><?php _e('Latitude Coordinates','dwpb') ?></th>
				<td>

<input name="prayer_lat"  type="text" value="<?php  echo get_option('prayer_lat'); ?>"  size="20" >


				</td></tr><tr>


<th scope="row"><?php _e('Longitude Coordinates','dwpb') ?></th>
				<td>

<input name="prayer_long"  type="text" value="<?php  echo get_option('prayer_long'); ?>"  size="20" >


								</td></tr>
			<tr>
				<th scope="row"><span class="dashicons dashicons-clock"></span> Jummah Settings</th>
			

</tr>
				
				


<th colspan="2"><div class="update-nag notice">
    <p>Please note all fields are not necessary. Input jummah timings according to your masjid.</p>
</div></th>

 <tr>

<th>1) Khutbah&nbsp;&nbsp;<input style="width:50%;" type="text" name="prayer_khutbah1" value="<?php  echo get_option('prayer_khutbah1'); ?>"> </th>
				<td>

<b>Iqamah&nbsp;&nbsp;<input style="width:110px;" type="text" name="prayer_iqamah1" value="<?php  echo get_option('prayer_iqamah1'); ?>"> 

				</td></tr><tr>
<th>2) Khutbah&nbsp;&nbsp;<input style="width:50%;" type="text" name="prayer_khutbah2" value="<?php  echo get_option('prayer_khutbah2'); ?>"> </th>
				<td>

<b>Iqamah&nbsp;&nbsp;<input style="width:110px;" type="text" name="prayer_iqamah2" value="<?php  echo get_option('prayer_iqamah2'); ?>"> 

				</td></tr><tr>
<th>3) Khutbah&nbsp;&nbsp;<input style="width:50%;" type="text" name="prayer_khutbah3" value="<?php  echo get_option('prayer_khutbah3'); ?>"> </th>
				<td>

<b>Iqamah&nbsp;&nbsp;<input style="width:110px;" type="text" name="prayer_iqamah3" value="<?php  echo get_option('prayer_iqamah3'); ?>"> 

				</td></tr><tr><th scope="row"><span class="dashicons dashicons-admin-customizer"></span>Style Settings</tr></th>
							
</tr><tr>


<th scope="row"><?php _e('Current Day/Time','dwpb') ?></th>
				<td><input class="regular-text color_picker dwpb_background_color" type="text" name="sw_current_color" value="<?php echo get_option('sw_current_color'); ?>" /></td></tr>
				
				<tr>
</td></tr><tr><th scope="row">Monthly Timetable</tr></th>


<th scope="row"><?php _e('Font Size','dwpb') ?></th>
				<td><input style="width:50px;" type="text" name="sw_month_size" value="<?php echo get_option('sw_month_size'); ?>" /></td></tr><tr>

<th scope="row"><?php _e('Hover','dwpb') ?></th>
				<td><input class="regular-text color_picker dwpb_background_color" type="text" name="sw_hover_color" value="<?php echo get_option('sw_hover_color'); ?>" /></td></tr>
				
				<tr>


<th scope="row"><?php _e('Days Color','dwpb') ?></th>
				<td><input class="regular-text color_picker dwpb_background_color" type="text" name="sw_day_color" value="<?php echo get_option('sw_day_color'); ?>" /></td></tr>
				
				<tr>


<th scope="row"><?php _e('Title','dwpb') ?></th>
				<td><input class="regular-text color_picker dwpb_background_color" type="text" name="sw_title_color" value="<?php echo get_option('sw_title_color'); ?>" /></td></tr>
				
				<tr>


<th scope="row"><?php _e('Sticky Header','dwpb') ?></th>
				<td><input class="regular-text color_picker dwpb_background_color" type="text" name="sw_sticky_color" value="<?php echo get_option('sw_sticky_color'); ?>" /></td></tr>
			
		</table>
	</div>

    <?php submit_button(); ?>
    
</form>
</div>

                                    </div>
                                </div>
                            </div>




                     

                        



                     

                        </div> <!--post-body-content-->


                        <!-- sidebar -->
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="meta-box-sortables">
				<div class="postbox">
                                    <h3><span>Customized Website for your Masjid</span></h3>
                                    <div class="inside">
                                     <img src="<?php echo plugins_url( 'salah-world-prayer-iqamah-timings-for-your-masjids/img/responsive.png', dirname(__FILE__) )?>" style="width: 100%; height: 100%" >
                                   <a href="https://mail.google.com/mail/?view=cm&fs=1&to=waseemm1975@gmail.com&su=Masjid Website via Salah World&body=
As-salâmu 'alaikum wa rahmatullâhi wa barakâtuhu,

" target="_blank">
                                   <a href="https://mail.google.com/mail/?view=cm&fs=1&to=waseemm1975@gmail.com&su=Masjid Website via Salah World&body=
As-salâmu 'alaikum wa rahmatullâhi wa barakâtuhu,

" target="_blank"> <h3><span>Email us.</span></h3></a>

                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

                                <div class="postbox">
                                    <h3><span>Rate This Plugin!</span></h3>
                                    <div class="inside">
                                      <p>Please <a href="https://wordpress.org/support/view/plugin-reviews/salah-world-prayer-iqamah-timings-for-your-masjids" target="_blank">rate this plugin</a> and share it to help the development.</p>

                                      <ul class="soc">
                                        <li><a class="soc-facebook" href="https://www.facebook.com" target="_blank"></a></li>
                                        <li><a class="soc-twitter" href="https://twitter.com" target="_blank"></a></li>
                                        <li><a class="soc-google soc-icon-last" href="https://plus.google.com/" target="_blank"></a></li>
                                      </ul>

                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

 				


                                <div class="postbox">
<img src="<?php echo plugins_url( 'salah-world-prayer-iqamah-timings-for-your-masjids/img/tumblr_n6hwk17T8q1s4shrvo1_1280.jpg"', dirname(__FILE__) )?>" style="width: 100%; height: 100%" target="_blank">		
					
                                   <a href="https://muslimhands.org.uk/" target="_blank"> <h3><span>Donate Now!</span></h3></a>
                                    <div class="inside">
                                     
                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

				
                            </div> <!-- .meta-box-sortables -->
                        </div> <!-- #postbox-container-1 .postbox-container -->


                    </div>
                </div>
            </div>
        <?php

    }



public function create_admin_page_poo (){



        $this->options = get_option ( 'fluid_notification_bar_settings' );
      
        ?>
            <div class="wrap">

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">


                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <div class="postbox">
                                    <h3><span class="dashicons dashicons-editor-help"></span>About</h3>
                                    <div class="inside">
                                 





<div class="wrap">
<p>Salah World is a small organization devoted in helping local Masjids keep a close link with their communities. We at Salah World are delighted to announce the launch of our new campaign whereby we will be giving away customized apps to masjid around the globe for free, fiysabilillah inshallah. Each Masjid will have their an app available on the Play Store and App Store - notifying the community when iqamah timings changes, when a new event is around the corner or simply if there is anything new.</p>

<p>In order to apply for a Free Masjid app please subscribe at our<a href="http://salahworld.org/"> website.</a> We will then send you all the details by email inshallah once we are done.</p>

<p><b>Please don’t forget to share this page with with your FACEBOOK & TWITTER followers :)</p></b>
		
	
	
		

</div> </div>
<h3><span class="dashicons dashicons-awards"></span>Credit</h3>
                                    <div class="inside">
                                 





<div class="wrap">
<p>Thanks to Sahaab ibn Zaid, Danial Ahmad, and many others.May <b>Allah</b> reward them with good in this life and the next and accept it from them.</p>
<p><a href="https://wordpress.org/plugins/dw-promobar/"><b>DW Promobar: </b></a> Custom notification bar for iqamah timings is based on DesignWall's DW Promobar plugin. For any bugs, request or issue please visit their <a href="https://wordpress.org/support/plugin/dw-promobar">support forum.</a></p>
<p><a href="http://praytimes.org/"><b>PrayTimes: </b></a> Prayer timings calculation is based on Brother Hamid Zarrabi-Zadeh's code. For more information or any related issue concerning the accuracy of prayer timings please <a href="http://praytimes.org/contact">contact them.</a></p>
		
	
	
		

</div> </div>


<h3><span class="dashicons dashicons-megaphone"></span>We Need your help!</h3>
                                    <div class="inside">
                                 





<div class="wrap">
<p>A lot of work has already gone into Salah World, but we have much bigger plans for it!<br>

So if you'd like to be part of the project, please check out the roadmap and issues to see if there's anything you can help with.</p>
		
	
	
		

</div> </div>

<h3><span class="dashicons dashicons-sos"></span>License</h3>
                                    <div class="inside">
                                 





<div class="wrap">
<p>This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.</p>

<p>This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.</p>

<p>You should have received a copy of the GNU General Public License
along with this program.  If not, see it <a href="http://www.gnu.org/licenses/">here.</a></p>
		
	
	
		

</div> </div>

<h3><span class="dashicons dashicons-heart"></span>Donate</h3>
                                    <div class="inside">
                                 





<div class="wrap">
<p>Salah World will be free forever, in sha Allah, but you can help us speed-up the development!</p>
<p>The <b>best</b> way you can you help us, is by making dua for us and the ummah as whole. </p>	
<p>If you want to contribute to us, we prefer you send the money to a charity organization on our behalf.</p>
	
	
		

</div> </div>

                                   
                                </div>
                            </div>




                     

                        



                     

                        </div> <!--post-body-content-->


                        <!-- sidebar -->
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="meta-box-sortables">
				<div class="postbox">
                                    <h3><span>Masjid Clock</span></h3>
                                    <div class="inside">
 <img src="<?php echo plugins_url( 'salah-world-prayer-iqamah-timings-for-your-masjids/img/c42bf4e9-b376-4c43-9f59-880e54c6e52b.jpg', dirname(__FILE__) )?>" style="width: 100%; height: 100%" >
                                   <a href="https://mail.google.com/mail/?view=cm&fs=1&to=waseemm1975@gmail.com&su=Masjid Clock via Salah World&body=
As-salâmu 'alaikum wa rahmatullâhi wa barakâtuhu,

" target="_blank">
                                    
                                    <h3><span>Sign up Now!</span></h3></a>

                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

                                <div class="postbox">
                                    <h3><span>Rate This Plugin!</span></h3>
                                    <div class="inside">
                                      <p>Please <a href="https://wordpress.org/support/view/plugin-reviews/salah-world-prayer-iqamah-timings-for-your-masjids" target="_blank">rate this plugin</a> and share it to help the development.</p>

                                      <ul class="soc">
                                        <li><a class="soc-facebook" href="https://www.facebook.com" target="_blank"></a></li>
                                        <li><a class="soc-twitter" href="https://twitter.com" target="_blank"></a></li>
                                        <li><a class="soc-google soc-icon-last" href="https://plus.google.com/" target="_blank"></a></li>
                                      </ul>

                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

 				


                                <div class="postbox">
<img src="<?php echo plugins_url( 'salah-world-prayer-iqamah-timings-for-your-masjids/img/tumblr_ni8p8pAw7f1tnos2zo1_1280.jpg', dirname(__FILE__) )?>" style="width: 100%; height: 100%" target="_blank">					

                                   <a href="https://muslimhands.org.uk/" target="_blank"> <h3><span>Donate Now!</span></h3></a>
                                    <div class="inside">
                                     
                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

				
                            </div> <!-- .meta-box-sortables -->
                        </div> <!-- #postbox-container-1 .postbox-container -->


                    </div>
                </div>
            </div>
        <?php

    }




    public function register_admin_scripts(){
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'Fnbar_admin', plugins_url('js/fnbar_admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
        wp_enqueue_style( 'custom-admin-style', plugins_url('css/admin-style.css', __FILE__));
        wp_enqueue_script('jquery-ui-datepicker');

	wp_enqueue_script('e2b-admin-prayer-js',plugins_url('js/PrayTimes.js', __FILE__ ),false,"1.9.0",false);

	wp_enqueue_script("jquery");

	wp_enqueue_style('e2b-admin-ui-css',plugins_url('css/jquery-ui.css', __FILE__ ),false,"1.9.0",false);

	wp_enqueue_style('e2b-admfffin-ui-css',plugins_url('css/st.css', __FILE__ ),false,"1.9.0",false);

	}

    public function create_admin_page_fnbar (){

        $this->options = get_option ( 'fluid_notification_bar_settings' );
       global $wpdb;

	$yday= date("Y-m-d");
	$table_name = $wpdb->prefix . 'iqamahTimes';

	$rowsa = $wpdb->get_results( "SELECT * FROM ".$table_name." ORDER BY ".$table_name.".field_1 ASC ");


	$prev=new DateTime(date("Y-m-d"));
        ?>
            <div class="wrap">

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">


                        <div id="post-body-content">
                            <div class="meta-box-sortables ui-sortable">
                                <div class="postbox">
                                    <h3><span class="dashicons dashicons-admin-generic"></span>Iqamah Time Settings</h3>
                                    <div class="inside">
                                       <table id="iqamahTable" width="100%">
<tr>
<td><center><b>Date</center></td>
<td><center><b>Fajr</center></td>
<td><center><b>Zuhr</center></td>
<td><center><b>Asr</center></td>
<td><center><b>Magrib</center></td>
<td><center><b>Isha</center></td>
</tr><?php
$fajr="";
$zuhr="";
$asr="";
$magrib="";
$isha="";
	foreach ( $rowsa as $rowa) 
	{
		if($rowa->field_1>= $yday)
		{
			$blahDate= new DateTime($rowa->field_1);
			?>

			<tr>
				<td>
				<?php echo $prev->format("Y-m-d")." -"; ?>
				<input type="text"  id=<?php echo $rowa->id?> onchange="updateRow('field_1',<?php echo $rowa->id?>,this.value)" class="iqamah_datepicker" value="<?php echo $blahDate->format("Y-m-d");?>" style="width:65%;" >
				</td>
				
				<td>
				<input type="text" onchange="updateRow('fajr',<?php echo esc_textarea($rowa->id);?>,this.value);" 	style="width:100%;" value=<?php $fajr = $rowa->fajr; echo $fajr; ?>   >
				</td>
				
				<td>
				<input type="text"  onchange="updateRow('zuhr',<?php echo esc_textarea($rowa->id);?>,this.value)"  style="width:100%;" value=<?php $zuhr = $rowa->zuhr; echo $zuhr;?> >
				</td>
				
				<td>
				<input type="text"  onchange="updateRow('asr',<?php echo esc_textarea($rowa->id);?>,this.value)"	style="width:100%;" value=<?php $asr = $rowa->asr; echo $asr;?> >
				</td>
				
				<td>
				<input type="text"  onchange="updateRow('magrib',<?php echo esc_textarea($rowa->id);?>,this.value)"  style="width:100%;" value=<?php $magrib = $rowa->magrib; echo $magrib;?> >
				</td>
				
				<td>
				<input type="text"  onchange="updateRow('isha',<?php echo esc_textarea($rowa->id);?>,this.value)"  style="width:100%;" value=<?php $isha = $rowa->isha; echo $isha;?> >
				</td>
				
			<input type="hidden" name='id' value=<?php echo esc_textarea($rowa->id);?>>
			
				<td>
				<img src="https://cdn3.iconfinder.com/data/icons/softwaredemo/PNG/128x128/DeleteRed.png" onclick="deleteRow(<?php echo esc_js($rowa->id);?>)"  style="width:24px;height:24px;" ><br>
				</td>
			
			</tr>

		<?php $blahDate=$blahDate->format("Y-m-d");
		$prev=new DateTime(date("Y-m-d",strtotime($blahDate . "+1 days")));
		}


	} 
$blat=0;?>
<tr><td colspan = "8"><center>
<button class="button button-primary" onclick="this.style.visibility='hidden';  myFunction()">Add Iqamah Times</button>
<div id="selectDate" style="display: none;">
    Box shadows are pretty cool.
</div>
</center></td></tr></table >
<script>

</script>
<script>
jQuery(document).ready(function( $ ) {
    
   jQuery(".iqamah_datepicker").datepicker({
     
        
        //The calendar is recreated OnSelect for inline calendar
        onSelect: function (date, dp) {
           updateDatePickerCells();
            var val=this.value;
            var id=this.id;
            updateRow('field_1',id,val);
           
              
        },
        onChangeMonthYear: function(month, year, dp) {
            updateDatePickerCells();
            
        },
        beforeShow: function(elem, dp) { //This is for non-inline datepicker
            updateDatePickerCells();
            
         },   
        
        dateFormat : 'yy-mm-dd'
   
    });
   updateDatePickerCells();
   
  
function updateDatePickerCells(dp) {
    /* Wait until current callstack is finished so the datepicker
       is fully rendered before attempting to modify contents */
    setTimeout(function () {
              
            jQuery('.ui-datepicker-calendar').after('<div class="className"></div>');
        
    }, 0);
}

    jQuery(".ui-state-default").live("mouseenter", function() {
    var date = new Date(jQuery(".ui-datepicker-month",jQuery(this).parents()).text()+ " " + jQuery(this).text() +","+jQuery(".ui-datepicker-year",jQuery(this).parents()).text()); // today

         prayTimes.setMethod('<?php 
$acms = array(
    '2' => 'ISNA',
    '5' => 'Egypt',
    '4' => 'Makkah',
    '3' => 'MWL',
    '1' => 'Karachi'
);
echo($acms[get_option('prayer_method')]);?>'); 
var tomorrow = date;
tomorrow.setDate(tomorrow.getDate() + 1);
if(tomorrow.getTimezoneOffset()!=date.getTimezoneOffset())
var tzOff=(tomorrow.getTimezoneOffset()/60)*-1;
else
var tzOff=(date.getTimezoneOffset()/60)*-1;
	var times = prayTimes.getTimes(date, [<?php echo(get_option('prayer_lat'));?>, <?php echo(get_option('prayer_long'));?>], tzOff, 0,"12hNS");    
        
        jQuery('.className').html("<table style=\"width:100%\"><tr><td><center><p class=\"custom\">Fajr<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Sun<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Zuhr<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Asr<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Magrib<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Isha<hr style=\"padding:0px; margin:0px;\"></td> </tr></p> <tr><td><center><p class=\"times\">"+times.fajr +"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"stimes\">"+times.sunrise+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"times\">"+times.zuhr+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"times\">"+times.asr+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"stimes\">"+times.magrib+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"times\">"+times.isha+"<hr style=\"padding:0px; margin:0px;\"></td> </tr></table></center>");
    });
});

</script>
	<script>
		jQuery(function()
		{
		   jQuery( ".iqamah_datepicker" ).datepicker({});
		});
	</script> 
<script>
function myFunction() {


    var table = document.getElementById("iqamahTable");
var x = document.getElementById("iqamahTable").rows.length;
    var row = table.insertRow(x-1);
    var cell1 = row.insertCell(0);
var cell2 = row.insertCell(1);
 var cell3 = row.insertCell(2);
     var cell4 = row.insertCell(3);
 var cell5 = row.insertCell(4);
 var cell6 = row.insertCell(5);
<?php $prev=$prev->format("Y-m-d");
		$prev1=new DateTime(date("Y-m-d",strtotime($prev. "+1 days")));?>

  cell1.innerHTML = "<?php echo $prev." - "; ?><input type='text' class='iq_datepicker' value=<?php echo $prev1->format("Y-m-d");?>>";
    cell2.innerHTML = "<input type='text' READONLY style=\"width:100%;\" value=<?php echo $fajr;?>>";
    cell3.innerHTML = "<input type='text' READONLY style=\"width:100%;\" value=<?php echo $zuhr;?>>";
    cell4.innerHTML = "<input type='text' READONLY style=\"width:100%;\" value=<?php echo $asr;?> >";
    cell5.innerHTML = "<input type='text' READONLY style=\"width:100%;\" value=<?php echo $magrib;?> >";
    cell6.innerHTML = "<input type='text' READONLY style=\"width:100%;\" value=<?php echo $isha;?> >";
 
}
jQuery(document).on('focus', '.iq_datepicker', function () {

	jQuery(this).datepicker({
	onSelect: function (date, dp) {
           updateDatePickerCells();
          var val=this.value;
      newRow(val);          
          
              
        },
beforeShow: function(elem, dp) { //This is for non-inline datepicker
            updateDatePickerCells();
            
         },       onChangeMonthYear: function(month, year, dp) {
            updateDatePickerCells();
            
        },
        dateFormat : 'yy-mm-dd'});
         
   
function newRow(val) 
		{
			
			jQuery.ajax(
			{
                url:ajaxurl,
                data: {
					'action':'new_row',
					'value':val,
					'fajr':'<?php echo $fajr?>',
					'zuhr':'<?php echo $zuhr?>',
					'asr':'<?php echo $asr?>',
					'magrib':'<?php echo $magrib?>',
					'isha':'<?php echo $isha?>'
				},
				success:function(data)
				{    
					
						location.reload(true);
					
				}
            });
			
		}
		
function updateDatePickerCells(dp) {
    /* Wait until current callstack is finished so the datepicker
       is fully rendered before attempting to modify contents */
    setTimeout(function () {
              
            jQuery('.ui-datepicker-calendar').after('<div class="className"></div>');
        
    }, 0);
}
		
		
    jQuery(".ui-state-default").live("mouseenter", function() {
    var date = new Date(jQuery(".ui-datepicker-month",jQuery(this).parents()).text()+ " " + jQuery(this).text() +","+jQuery(".ui-datepicker-year",jQuery(this).parents()).text()); // today
         prayTimes.setMethod('<?php echo(get_option('prayer_method'));?>'); 

	var times = prayTimes.getTimes(date, [<?php echo(get_option('prayer_lat'));?>, -74.3664700], -4,0 ,"12hNS");    
          
        jQuery('.className').html("<table style=\"width:100%\"><tr><td><center><p class=\"custom\"> Fajr<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Sun<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Zuhr<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Asr<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Magrib<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"custom\">Isha<hr style=\"padding:0px; margin:0px;\"></td> </tr></p> <tr><td><center><p class=\"times\">"+times.fajr +"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"stimes\">"+times.sunrise+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"times\">"+times.zuhr+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"times\">"+times.asr+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"stimes\">"+times.magrib+"<hr style=\"padding:0px; margin:0px;\"></td><td><center><p class=\"times\">"+times.isha+"<hr style=\"padding:0px; margin:0px;\"></td> </tr></table></center>");
    });

});
		

</script>

<script>
function deleteRow(val) {
		
			var url = val;
			//alert (url);
			jQuery.ajax(
			{
                url:ajaxurl,
                data: {
					'action':'delete_row',
					'id'    : url
				},
				success:function(data)
				{    
					location.reload(true);
				}
            });
	}
</script>
<?php


?>
	<script>
              
		function updateRow(field,id,val) 
		{
	
			jQuery.ajax(
			{
                url:ajaxurl,
                data: {
					'action':'update_row',
					'name':field,
					'id':id,
					'value':val
				},
				success:function(data)
				{    
					var str1 = "field_1";
					var n = str1.localeCompare(field);
					if(n==0)
					{
						location.reload(true);
					}
					
				}
            });
		}
		
	</script> 
                                    </div>
                                </div>
                            </div>
                        </div> <!--post-body-content-->


                        <!-- sidebar -->
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="meta-box-sortables">
				<div class="postbox">
                                    <h3><span>Customized App for your Masjid</span></h3>
                                    <div class="inside">
                                     <img src="<?php echo plugins_url( 'salah-world-prayer-iqamah-timings-for-your-masjids/img/masjid-app.png', dirname(__FILE__) )?>" style="width: 100%; height: 100%" >
                                   <a href="https://mail.google.com/mail/?view=cm&fs=1&to=waseemm1975@gmail.com&su=Masjid App via Salah World&body=
As-salâmu 'alaikum wa rahmatullâhi wa barakâtuhu, &bcc=Sahaabzahid@gmail.com

" target="_blank"> <h3><span>Sign up Now!</span></h3></a>

                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

                                <div class="postbox">
                                    <h3><span>Rate This Plugin!</span></h3>
                                    <div class="inside">
                                      <p>Please <a href="https://wordpress.org/support/view/plugin-reviews/salah-world-prayer-iqamah-timings-for-your-masjids" target="_blank">rate this plugin</a> and share it to help the development.</p>

                                      <ul class="soc">
                                        <li><a class="soc-facebook" href="https://www.facebook.com" target="_blank"></a></li>
                                        <li><a class="soc-twitter" href="https://twitter.com" target="_blank"></a></li>
                                        <li><a class="soc-google soc-icon-last" href="https://plus.google.com/" target="_blank"></a></li>
                                      </ul>

                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

 				


                                <div class="postbox">
					<img src="<?php echo plugins_url( 'salah-world-prayer-iqamah-timings-for-your-masjids/img/336ce4d0b8eaf441d99c48ec052eb7be.jpg', dirname(__FILE__) )?>" style="width: 100%; height: 100%" target="_blank">
                                   <a href="https://muslimhands.org.uk/" target="_blank"> <h3><span>Donate Now!</span></h3></a>
                                    <div class="inside">
                                     
                                    </div> <!-- .inside -->
                                </div> <!-- .postbox -->

				
                            </div> <!-- .meta-box-sortables -->
                        </div> <!-- #postbox-container-1 .postbox-container -->


                    </div>
                </div>
            </div>
        <?php

    }

} 

?>