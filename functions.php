<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// Call extra_post_info_menu function to load plugin menu in dashboard
//import_request_variables("p");
include('PrayTime.php');
wp_enqueue_script("jquery");

wp_enqueue_script('as', plugins_url('js/jquery.ba-throttle-debounce.min.js', __FILE__), false, "1.9.0", false);
wp_enqueue_script('e2b-admin-prayer-js', plugins_url('js/PrayTimes.js', __FILE__), false, "1.9.0", false);
wp_enqueue_script('e2b-month-prayer-js', plugins_url('js/jquery.stickyheader.js', __FILE__), false, "1.9.0", false);

wp_enqueue_style('custom-prayerIcon-style', plugins_url('css/weather-icons.min.css', __FILE__));
wp_enqueue_style('custom-componet-style', plugins_url('css/component.css', __FILE__));




function SW_dailyWidget($atts)
{
    
    $a = shortcode_atts(array(
        'icon' => 'true',
        'iqamah' => 'true',
        'prayer' => 'true'
    ), $atts);
    
    $method    = get_option('prayer_method');
    $latitude  = get_option('prayer_lat');
    $longitude = get_option('prayer_long');
    
    $prayTime = new PrayTime($method);
    $prayTime->setAsrMethod(get_option(0));
    
    $dtz           = new DateTimeZone(get_option("timezone_string"));
    $time_in_sofia = new DateTime('now', $dtz);
    $offset        = $dtz->getOffset($time_in_sofia) / 3600;
    $timeZone      = ($offset < 0 ? $offset : "+" . $offset);
    
    $date    = strtotime(date("d.m.Y"));
    $endDate = strtotime('+1 day', $date);
    
    $times = $prayTime->getPrayerTimes($date, $latitude, $longitude, $timeZone, 0, "12hNS");
    $prayTime->setTimeFormat(0);
    $timesValue = $prayTime->getPrayerTimes($date, $latitude, $longitude, $timeZone, 0, "12hNS");
    
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'iqamahTimes';
    $output     = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY " . $table_name . ".field_1 ASC ", 'ARRAY_A');
    $foo = reset(array_filter($output, function($var)
    {
        return ($var[field_1] >= date("Y-m-d"));
    }));
    $day = date('l, F d', $date);
    
    if ($a['icon'] == "true") {
        $iconString = array(
            '<td><center><i class="wi wi-horizon"></center></td>',
            '<td><center><i class="wi wi-sunrise"></center></td>',
            '<td><center><i class="wi wi-day-sunny"></center></td>',
            '<td><center><i class="wi wi-horizon-alt"></center></td>',
            '<td><center><i class="wi wi-horizon-alt"></center></td>',
            '<td><center><i class="wi wi-moon-full"></center></td>'
        );
        $boo++;
    }
    
    if ($a['prayer'] == "true") {
        $prayerString = array(
            '<td><center>' . $times[0] . '</center></td>',
            '<td><center>' . $times[2] . '</center></td>',
            '<td><center>' . $times[3] . '</center></td>',
            '<td><center>' . $times[4] . '</center></td>',
            '<td><center>' . $times[6] . '</center></td>'
        );
        $boo++;
    }
    
    if ($a['iqamah'] == "true") {
        $iqamahString = array(
            '<td><center>' . $foo[fajr] . '</center></td>',
            '<td><center>' . $foo[zuhr] . '</center></td>',
            '<td><center>' . $foo[asr] . '</center></td>',
            '<td><center>' . $foo[isha] . '</center></td>'
        );
        
        if ($boo == 1)
            $iSpace = array(
                '<td></td>',
                '<td><center>Jummah</center></td>'
            );
        
        if ($boo == 2)
            $iSpace = array(
                '<td colspan="2"></td>',
                '<td colspan="2"><center>Jummah</center></td>'
            );
        
        if ($boo >= 0)
            $spacer = 2;
        
        if ($boo != 0)
            $oJum = '<td colspan="' . $boo . '"><center>Jummah</center></td>';
        
        if (get_option('prayer_khutbah1')) {
            if (get_option('prayer_iqamah1')) {
                $jumString = ' 
				<tr>
					' . $iSpace[0] . '
					<td><center>Khutbah</center></td>	
					<td><center>Iqamah</center></td>
				</tr>
				<tr>
					' . $iSpace[1] . '
					<td><center>' . get_option("prayer_khutbah1") . '</center></td>	
					<td><center>' . get_option("prayer_iqamah1") . '</center></td>
				</tr>';
            }
            
            else {
                $jumString = ' 
				<tr>
					' . $oJum . '
					<td colspan=' . $spacer . '><center>' . get_option("prayer_khutbah1") . '</center></td>	
				</tr>';
            }
        }
        
        if (get_option('prayer_khutbah2')) {
            if (get_option('prayer_iqamah2')) {
                $jumString = $jumString . ' 
				<tr>
					' . $iSpace[1] . '
					<td><center>' . get_option("prayer_khutbah2") . '</center></td>	
					<td><center>' . get_option("prayer_iqamah2") . '</center></td>
				</tr>';
            } else {
                $jumString = $jumString . '
				<tr>
					' . $oJum . '
					<td colspan=' . $spacer . '><center>' . get_option("prayer_khutbah2") . '</center></td>	
				</tr>';
            }
        }
        
        if (get_option('prayer_khutbah3')) {
            if (get_option('prayer_iqamah3')) {
                $jumString = $jumString . ' 
				<tr>
					' . $iSpace[1] . '
					<td><center>' . get_option("prayer_khutbah3") . '</center></td>	
					<td><center>' . get_option("prayer_iqamah3") . '</center></td>
				</tr>';
            } else {
                $jumString = $jumString . ' 
				<tr>
					' . $oJum . '
					<td colspan=' . $spacer . '><center>' . get_option("prayer_khutbah3") . '</center></td>	
				</tr>';
            }
        }
        
        $boo++;
    }
    
    if ($a['iqamah'] == "true") {
        
        if (strpos($foo[magrib], ':') !== false) {
            if ($a['prayer'] == "true")
                $magrib = '<td><center>' . $times[4] . '</center></td>
			               <td><center>' . $foo[magrib] . '</center></td>';
            else
                $magrib = '<td><center>' . $foo[magrib] . '</center></td>';
            
        } 
		else if ($foo[magrib] == "0") {
            if ($a['prayer'] == "true")
                $magrib = '<td colspan="2"><center>' . $times[4] . '</center></td>';
            else
                $magrib = '<td><center>' . $times[4] . '</center></td>';
        } 
		else {
            $time      = strtotime($times[4]);
            $startTime = date("g:i", strtotime('+' . $foo[magrib] . ' minutes', $time));
           
		   if ($a['prayer'] == "true")
                $magrib = '<td><center>' . $times[4] . '</center></td>
			               <td><center>' . $startTime . '</center></td>';
            else
                $magrib = '<td><center>' . $startTime . '</center></td>';
        }        
    } 
	
	else if ($a['prayer'] == "true") {
        $magrib = '<td><center>' . $times[4] . '</center></td>';
    }
	
    if ($a['iqamah'] == "true" && $a['prayer'] == "true")
        $sunString = ' 
		<tr value=' . $timesValue[1] . '>
			<td><center>Sunrise</center></td>
			' . $iconString[1] . '	
			<td colspan="2"><center>' . $times[1] . '</center></td>
		</tr>';
		
    else if ($a['prayer'] == "true")
        $sunString = '     
		<tr value=' . $timesValue[1] . '>
			<td><center>Sunrise</center></td>
			' . $iconString[1] . '	
			<td><center>' . $times[1] . '</center></td>
		</tr>';
    $swcurrent     = get_option("sw_current_color");
    $returnString = '
	<table class="iqamahTable" >
		<thead><tr>
			<td colspan=' . ++$boo . '><center> ' . $day . '</center></td>
		</tr></thead>
		<tbody>
			<tr value=' . $timesValue[0] . '>
				<td><center>Fajr</center></td>
				' . $iconString[0] . '
				' . $prayerString[0] . '
				' . $iqamahString[0] . '
			</tr>

			' . $sunString . '

			<tr value=' . $timesValue[2] . '>
				<td><center>Dhuhr</center></td>
				' . $iconString[2] . '
				' . $prayerString[1] . '	
				' . $iqamahString[1] . '        
			</tr>

			<tr value=' . $timesValue[3] . '>
				<td><center>Asr</center></td>
				' . $iconString[3] . '	
				' . $prayerString[2] . '
				' . $iqamahString[2] . '
			</tr>

			<tr value=' . $timesValue[4] . '>
				<td><center>Magrib</center></td>
				' . $iconString[4] . '	
				' . $magrib . '
			</tr>


			<tr value=' . $timesValue[6] . '>
				<td><center>Isha</center></td>
				' . $iconString[5] . '	
				' . $prayerString[4] . '
				' . $iqamahString[3] . '
			</tr>

			' . $jumString . '
			
		</tbody>
	</table>
	
	
	<script>

	var $htrue=false;     	                   	
	var now = new Date();
	var hours = now.getHours()+""+now.getMinutes();

	jQuery("table.iqamahTable").each(function(i,v){
		jQuery(this).find("tbody  > tr:first").addClass("hightlight");
	});
	jQuery("table.iqamahTable").each(function(i,v){

		jQuery(this).find("tbody  > tr").each(function(i,v){
			var pla = jQuery(this).attr("value");
			if(pla.replace(":","")>=hours) {    
				jQuery(this).addClass("hightlight");
				jQuery(".iqamahTable > tbody  > tr:first").removeClass("hightlight");
				$htrue=true;return false;
			}		
		});
	});

	if($htrue) {
		jQuery("table.iqamahTable").each(function(i,v){
			jQuery(this).find("tbody  > tr:first").removeClass("hightlight");
		});
	}
	</script>
		<style>

		.hightlight{
			background: ' . $swcurrent . ' !important;
			color: #f7f7f7;
		}
		

	</style>';
    
    return $returnString;
}

function SW_monthlyWidget($atts)
{
    
    $a = shortcode_atts(array(
        'iqamah' => 'true',
        'prayer' => 'true'
    ), $atts);
    
    $method        = get_option('prayer_method');
    $latitude      = get_option('prayer_lat');
    $dtz           = new DateTimeZone(get_option("timezone_string"));
    $time_in_sofia = new DateTime('now', $dtz);
    $offset        = $dtz->getOffset($time_in_sofia) / 3600;
    $timeZone      = ($offset < 0 ? $offset : "+" . $offset);
    $longitude     = get_option('prayer_long');
    $acms          = array(
        '2' => 'ISNA',
        '5' => 'Egypt',
        '4' => 'Makkah',
        '3' => 'MWL',
        '1' => 'Karachi'
    );
    $method        = $acms[get_option('prayer_method')];
    $bcolor        = get_option('dwpb_background_color');
    $swday         = (get_option("sw_day_color"));
    $swcurrent     = get_option("sw_current_color");
    $swhover       = get_option("sw_hover_color");
    $swtitle       = get_option("sw_title_color");
    $swsticky      = get_option("sw_sticky_color");
    $swmonth       = get_option("sw_month_size");
    
	//Iqamah Timings From Database
    global $wpdb;
    $table_name    = $wpdb->prefix . 'iqamahTimes';
    $output = $wpdb->get_results("SELECT * FROM " . $table_name . " ORDER BY " . $table_name . ".field_1 ASC ", 'ARRAY_A');
    
    
    
    $iArray = (json_encode(array_values($output)));
    wp_register_script('monthly_script', plugins_url('js/monthly.js', __FILE__));
    $params = array(
        'method' => $method,
        'lat' => $latitude,
        'long' => $longitude,
        'timeZone' => $timeZone,
        'array' => $iArray
    );
    wp_localize_script('monthly_script', 'monthly_settings', $params);
    wp_enqueue_script('monthly_script');
    
	
	$returnString = '

	<table align="center" class="monthlyheaderTable">
		<tr>
			<td><a href="javascript:displayMonth(-1)" class="arrow">&lt;&lt;</a></td>
			<td  class="caption"><div id="table-title" align="center"></div> </td>
			<td><div align="right"><a href="javascript:displayMonth(+1)" class="arrow">&gt;&gt;</a></div></td>
		</tr>
	</table>

	<table  border="0" cellpadding="0" cellspacing="0" id="myTable" class="myTable flat-table flat-table-1 flatTable fixed_width" width="100%">
		<thead><tr>
				<th><center>Day</center></th>
				<th colspan="2"><center>Fajr</center></th>
				<th><center>S.Rise</center></th>
				<th colspan="2"><center>Dhuhr</center></th>
				<th colspan="2"><center>Asr</center></th>
				<th colspan="2"><center>Maghrib</center></th>
				<th colspan="2"><center>Isha</center></th>
		</tr></thead>

		<tbody id="tbodyid"> </tbody>

	</table>

	<style>
		.myTable td.long { 
			color: #a8a8a8;  
			vertical-align: bottom;
		}

		.myTable tr:hover td.long { 
			color: black;font-weight: bold;
		}
	</style>

	<style>
		.myTable  .koo {
			background-color: ' . $swday . ' ;
		}
		.arrow {
			font-weight: bold; 
			text-decoration: none; 
			color: ' . $swtitle . ' !important; 
		}
			
		.arrow:hover {
			text-decoration: underline !important;
		}

		.caption {
			font-size: 20px; 
			color: ' . $swtitle . '; 
			text-align: center; 
			font-weight: bold; 
		}
			
		table.sticky-thead th {
			background-color: ' . $swsticky . ' ;
			font-weight: bold ;
			font-size: ' . ($swmonth) . 'px !important;
			color: #fff ;
		}
		
		#myTable tbody :hover .koo {
			background-color: ' . $swhover . ' !important;
		}
		
		@media all and (min-width: 1400px){
			#myTable tbody :hover .today-row {
			  background-color: ' . $swhover . ' !important;
			}

			#myTable tbody :hover .longtoday-row {
			  background-color: ' . $swhover . ' !important;
			}

			#myTable tbody :hover {
			   background: ' . $swhover . ' !important; 
			}
		}

		.today-row {
			background-color: ' . $swcurrent . ' !important; 
			color: white; 
		}
		
		.longtoday-row {
			background-color: ' . $swcurrent . '; 
			color: white; 
			font-weight: bold;
		}
			
		#myTable #tbodyid {
			font-size: ' . $swmonth . 'px !important;
		}
	</style>';
    
    return $returnString;
}

add_shortcode('dailySalah', 'SW_dailyWidget');
add_shortcode('monthlySalah', 'SW_monthlyWidget');
?>