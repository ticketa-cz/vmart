<?php
/*
Plugin Name: Ticketa Daily SMS
Description: Ticketa agent field plus daily schedule sending
*/

//define( 'WP_DEBUG', true );
require_once ('includes/Tax-meta-class/Tax-meta-class.php');

if (is_admin()){

	$prefix = 'tc_';
	
	
	///////////////// ARTIST ADDITIONAL FIELDS //////////////////
	
	$artist_config = array(
	'id' => 'artist_meta_box',          // meta box id, unique per meta box
	'title' => 'Artist Meta Box',          // meta box title
	'pages' => array('event_category'),        // taxonomy name, accept categories, post_tag and custom taxonomies
	'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
	'fields' => array(),            // list of meta fields (can be added by field arrays)
	'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	$artist_meta = new Tax_Meta_Class($artist_config);
	$artist_meta->addText($prefix.'agent_jmeno',array('name'=> __('Jméno road managera ','tax-meta')));
	$artist_meta->addText($prefix.'agent_cislo',array('name'=> __('Telefon ','tax-meta'),'desc' => 'zadejte telefonní číslo ve formátu 777 123 123 bez předvolby'));
	$artist_meta->addText($prefix.'agent_email',array('name'=> __('Email ','tax-meta')));
	
	$artist_meta->addText($prefix.'ico',array('name'=> __('IČO ','tax-meta')));
	$artist_meta->addText($prefix.'zastupitel',array('name'=> __('Zastupitel ','tax-meta')));
	
	$repeater_fields[] = $artist_meta->addText($prefix.'umelec_jmeno',array('name'=> __('Jméno ','tax-meta')),true);
	$repeater_fields[] = $artist_meta->addText($prefix.'umelec_cislo',array('name'=> __('Telefon ','tax-meta')),true);
	$repeater_fields[] = $artist_meta->addText($prefix.'umelec_email',array('name'=> __('Email ','tax-meta')),true);
	$repeater_fields[] = $artist_meta->addCheckbox($prefix.'umelec_posilat', array('name' => __('Posílat upozornění ','tax-meta')),true);
		 
	$artist_meta->addRepeaterBlock($prefix.'umelci_kontakty',array('inline' => false, 'name' => __('Dodatečné kontakty','tax-meta'),'fields' => $repeater_fields));
	
	$artist_meta->addText($prefix.'ucetni_stredisko',array('name'=> __('Účetní středisko ','tax-meta')));
	
	$artist_meta->Finish();

}

////////////// KAZDODENNI SMS a EMAILY /////////////////////

function today_event_query() { 
	ob_start();
    
   //db time - 2017-02-20 19:00
   $today = new DateTime('today');
   $todaydate = $today->format('Y-m-d H:i');
   $tomorrow = $today->modify('+1 day');
   $tomorrowdate = $tomorrow->format('Y-m-d H:i');
	
		echo '<br/>'.$todaydate.'<br/>'.$tomorrowdate.'<br/><br/>';		
	
   $args = array(
        'post_type' => 'tc_events',
        'meta_query' => false,
        'order' => 'ASC',
        'orderby' => 'meta_value',
        'post_status' => 'publish',
		'tax_query' => false
    );	

	$args["meta_query"] = array(
		'relation' => 'AND',
		array(
			'key' => 'event_date_time',
			'value' => $todaydate,
			'type' => 'DATETIME',
			'compare' => '>='
		),
		array(
			'key' => 'event_date_time',
			'value' => $tomorrowdate,
			'type' => 'DATETIME',
			'compare' => '<='
		),
		'orderby' => 'event_date_time',
	);

    // The Query
    $the_query = new WP_Query($args);
	
    // The Loop
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
			$id = get_the_ID();
			
			$event_date_time_raw = get_post_meta($id, 'event_date_time', true);
            $date = date_i18n(get_option('date_format'), strtotime($event_date_time_raw));
            $time = date_i18n(get_option('time_format'), strtotime($event_date_time_raw));
					
			$locations = get_the_terms( $id , 'event_location' );
			$loc = array_pop($locations);
			$locid = $loc->term_id;
			
			$location_address = get_term_meta($locid, 'tc_adresa_zarizeni', true);
			$location_jmeno = get_term_meta($locid, 'tc_produkce-jmeno', true);
			$location_cislo = get_term_meta($locid, 'tc_produkce-tel', true);
			$location_email = get_term_meta($locid, 'tc_produkce-email', true);
												
			$artists = get_the_terms( $id , 'event_category' );
		
			if ( ! empty( $artists ) && is_array( $artists ) ) {
				foreach ($artists as $art) {
					
					$artid = $art->term_id;
					
					$artist_name = $art->name;
					$agent_jmeno = get_term_meta($artid, 'tc_agent_jmeno', true);
					$agent_tel = get_term_meta($artid, 'tc_agent_cislo', true);
					$agent_cislo = str_replace(' ', '', $agent_tel);
					$agent_email = get_term_meta($artid, 'tc_agent_email', true);
					
					//// ODESLANI ROAD MANAGEROVI ////	
										
					odeslat_email($date, $time, $artist_name, $location_address, $location_jmeno, $location_cislo, $location_email, $agent_email);
					odeslat_sms($date, $time, $artist_name, $location_address, $location_jmeno, $location_cislo, $location_email, $agent_cislo);
					
					/////// NACTENI DALSICH KONTAKTU K ODESLANI ///////
					
					$kontakty_umelcu = get_term_meta( $artid, 'tc_umelci_kontakty');
					
					if ( ! empty( $kontakty_umelcu ) && is_array( $kontakty_umelcu ) ) {
						foreach ($kontakty_umelcu as $kon) {
							foreach ($kon as $kontakt) {
													
								$umelec_jmeno = $kontakt['tc_umelec_jmeno'];
								$umelec_cislo = str_replace(' ', '', $kontakt['tc_umelec_cislo']);
								$umelec_email = $kontakt['tc_umelec_email'];
								$umelec_posilat = $kontakt['tc_umelec_posilat'];
								
								echo $umelec_posilat.' - '.$umelec_jmeno.' - '.$umelec_email.'<br/><br/><br/>';
								
								if ($umelec_posilat == 'on') {
									odeslat_email($date, $time, $artist_name, $location_address, $location_jmeno, $location_cislo, $location_email, $umelec_email);
									odeslat_sms($date, $time, $artist_name, $location_address, $location_jmeno, $location_cislo, $location_email, $umelec_cislo);							
								}
							}
						}
					}

					echo $id.' - '.$artid.' - '.$date.' - '.$time.' - '.$artist_name.' - '.$location_address.' - '.$location_jmeno.' - '.$location_cislo.' - '.$location_email.' - '.$agent_email.' - '.$agent_cislo.' - '.$agent_jmeno.'<br/>';
					
				}
			}
        }
    }
	//echo '<pre>'; print_r( _get_cron_array() ); echo '</pre>';
}


//////// ODESLAT MAIL ///////////

function odeslat_email($date, $time, $artist_name, $location_address, $location_jmeno, $location_cislo, $location_email, $agent_email) {
	$subject = $date.' v '.$time.' - '.$artist_name;
	$mailheaders = array('Content-Type: text/html; charset=UTF-8');
	$mailheaders[] = 'From: VMart <info@vm-art.cz>';
		
	/// obsah ///
	
	$agent = 'DIVADLO: Eva Bartošová, GSM: +420 732 456 994, EMAIL: divadlo@vm-art.cz <br/> HUDBA: Michal Kindl, GSM: +420 602 249 352, EMAIL: produkce@vm-art.cz';
	$obsah = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><style>'.file_get_contents('assets/export.css').'</style></head><body>';
	$obsah .= '<div class="exportdiv"><div class="exportlogo"></div><br/><h3 class="artisth3">'.$subject.'</h3>';
	$obsah .= '<table class="exporttable">';
	$obsah .= '<tr><td>Agent</td><td>'.$agent.'</td></tr>';
	$obsah .= '</table>';
	$obsah .= '<table class="exporttable">';
	$obsah .= '<tr><td><strong>Místo</strong></td><td><strong>'.$location_address.'</strong></td></tr>';
	$obsah .= '<tr><td>Jméno</td><td>'.$location_jmeno.'</td></tr>';
	$obsah .= '<tr><td>Telefon</td><td>'.$location_cislo.'</td></tr>';
	$obsah .= '<tr><td>Email</td><td>'.$location_email.'</td></tr>';
	$obsah .= '</table></div></body></html>';
	
	///$obsah .= '<style>'.file_get_contents('export.css').'</style>

	echo '<br/><br/><br/>'.$obsah.'<br/><br/><br/>';
		
	if ($agent_email && filter_var($agent_email, FILTER_VALIDATE_EMAIL)) {
		$send = wp_mail( $agent_email, $subject, $obsah, $mailheaders );
		if ($send) { echo '<br/><br/>odeslano<br/><br/>'; } else { echo '<br/><br/>nene<br/><br/>'; }
	}
}

///////// ODESLAT SMS ///////////

function odeslat_sms($date, $time, $artist_name, $location_address, $location_jmeno, $location_cislo, $location_email, $agent_cislo) {
	$smsmessage = 'Dobrý den, dnes '.$date.' v '.$time.' - '.$artist_name.' - '.$location_address.' - KONTAKT: '.$location_jmeno.' / '.$location_cislo;
	
	if ($agent_cislo) {
		do_action(
		  'woosms_send_sms',  // Action name
		  $agent_cislo,        // Phone number
		  $smsmessage,      // Message text with variables - <datum>
		  array(              // Variables to fill
			'datum' => $date,
			'cas'  => $time
		  ), 
		  array(                // Optional additional settings
			'unicode' => true,  // Unicode SMS
			'flash' => false,   // Flash SMS
			'country' => 'cz',  // ISO Code 3166 to fill the country prefix if the phone number is in national format (UNITED KINGDOM in this case)
			'senderType' => 'gText', // Sender type (gSystem => System number, gShort => Short code, gText => Alfa sender, gOwn => Numeric sender)
			'senderValue' => 'VMart' // Sender value
		  )
		);
	}
}


//// setup cron job ////
/*
register_activation_hook ( __FILE__, 'dailysms_activate' );
function dailysms_activate() {
	if ( ! wp_next_scheduled( 'daily_sms_hook' ) ) {
		wp_schedule_event( strtotime('08:00:00'), 'daily', 'daily_sms_hook' );
	}
}
add_action( 'daily_sms_hook', 'today_event_query' );
//// delete cron job ////
*/

wp_clear_scheduled_hook('daily_sms_hook');

add_shortcode('today_events', 'today_event_query');
?>