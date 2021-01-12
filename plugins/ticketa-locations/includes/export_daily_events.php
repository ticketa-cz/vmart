<?php

function tc_export_daily_events() {
	
	require_once 'includes/dompdf/autoload.inc.php';
	//use Dompdf\Dompdf;

	//query argumenets
    $args = array(
        'post_type' => 'tc_events',
        'order' => 'ASC',
        'orderby' => 'meta_value',
        'post_status' => 'publish'
    );
	$args["meta_query"] = array(
		'relation' => 'AND',
			array(
				'key' => 'event_date_time',
				'value' => date('Y-m-d'),
				'type' => 'DATETIME',
				'compare' => '='
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
			
			$artists = get_the_terms( $id , 'event_category' );
		
			if ( ! empty( $artists ) && is_array( $artists ) ) {
				foreach ($artists as $art) {
				    ob_start();

					$artid = $art->ID;
					
					$artist_name = $art->name;
					$agent_jmeno = get_term_meta($artid, 'tc_agent_jmeno', true);
					$agent_cislo = get_term_meta($artid, 'tc_agent_cislo', true);
					$agent_email = get_term_meta($artid, 'tc_agent_email', true);
					
					$locations = get_the_terms( $id , 'event_location' );
					$loc = array_pop($locations);
					$locid = $loc->ID;
					
					$location_address = get_term_meta($locid, 'tc_adresa_zarizeni', true);
					$location_jmeno = get_term_meta($locid, 'tc_produkce-jmeno', true);
					$location_cislo = get_term_meta($locid, 'tc_produkce-tel', true);
					$location_email = get_term_meta($locid, 'tc_produkce-email', true);
					
					
					//////// ODESLAT MAIL ///////////
					$subject = date('d. F Y').' - '.$artist_name;
					$message = '<br/><h3 style="text-align:center; width: 100%;">Informace o vaši dnešní události najdete v přiloženém PDF.</h3>';
					$headers = 'From: VM-art.cz <info@vm-art.cz>;';
					
					$agent = 'DIVADLO: Eva Bartošová, GSM: +420 732 456 994, E- MAIL: divadlo@vm-art.cz <br/> HUDBA: Michal Kindl, GSM: +420 602 249 352, E- MAIL: produkce@vm-art.cz';
					$obsah = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
					$obsah .= '<style>'.file_get_contents('../assets/export.css').'</style></head><body>';
					$obsah .= '<div class="exportdiv"><div class="exportlogo"></div><br/><h3 class="artisth3">'.$subject.'</h3>';
					$obsah .= '<table class="exporttable">';
					$obsah .= '<tr><td></td><td></td></tr>';
					$obsah .= '<tr><td>Agent</td><td>'.$agent.'</td></tr>';
					$obsah .= '</table>';
					
					$obsah .= '<table class="exporttable artist">';
					$obsah .= '<tr><td><strong>Akce</strong></td><td>'.get_the_title($id).'</td></tr>';
					$obsah .= '<tr><td><strong>Datum</strong></td><td>'.date('d. F Y').'</td></tr>';
					$obsah .= '<tr><td><strong>Místo</strong></td><td>'.$location_address.'</td></tr>';
					$obsah .= '<tr><td>Jméno</td><td>'.$location_jmeno.'</td></tr>';
					$obsah .= '<tr><td>Telefon</td><td>'.$location_cislo.'</td></tr>';
					$obsah .= '<tr><td>Email</td><td>'.$location_email.'</td></tr>';
					
					$obsah .= '</table></div></body></html>';
					
					echo $obsah;
									
					$jmeno = site_url().'/dailyPDF/'.$subject.'.pdf';
					$html = mb_convert_encoding($obsah, 'HTML-ENTITIES', 'UTF-8');
						
					$dompdf = new Dompdf($options);
					$dompdf->loadHtml(html_entity_decode($html));
					$dompdf->setPaper('A4', 'portrait');
					
					ob_end_clean();
					$dompdf->render();
   					$output = $dompdf->output();
    				$attachment = file_put_contents($jmeno, $output);
					
					//wp_mail( $agent_email, $subject, $message, $headers, $attachment );
					wp_mail( 'kotak@seznam.cz', $subject, $message, $headers, $attachment );
					
					///////// ODESLAT SMS ///////////
					
				}
			}
        }
    }
	
}
add_shortcode('export_daily_events_pdf', 'tc_export_daily_events_pdf');

	
?>