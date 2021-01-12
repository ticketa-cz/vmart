<?php

function tc_export_artist_pdf() {

	ob_start();

	$artid = $_GET["art_id"];
	$term = get_term( $artid, 'event_category' );
	$title = $term->name;
	$slug = str_replace("-kat","",$term->slug);

	$args = array(
			'post_type' => 'tc_events',
	        'posts_per_page' => 1000,
			'order' => 'ASC',
			'orderby' => 'meta_value',
			'post_status' => 'publish',
	);
	$args["meta_query"] = array(
					array(
						'key' => 'event_date_time',
						'value' => date('Y-m-d H:i'),
						'type' => 'DATETIME',
						'compare' => '>='
					),
					'orderby' => 'event_date_time',
	);
	$args["tax_query"] = array(
						array(
							'taxonomy' => 'event_category',
							'field' => 'term_id',
							'terms' => $artid,
						)
	);
	$event_query = new WP_Query($args);
	$pocetakci = $event_query->post_count;
	$agent = 'Michal Kindl, tel: +420 602 249 352, mail: produkce@vm-art.cz<br/>Barbara Novotná, tel: +420 727 950 785, mail: booking@vm-art.cz';
	
	$obsah = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
	$obsah .= '<style>'.file_get_contents('../assets/export.css').'</style></head><body>';
	$obsah .= '<div class="exportdiv"><div class="exportlogo"></div><br/><h3 class="artisth3">'.$title.'</h3>';
					
		$obsah .= '<table class="exporttable">';
		$obsah .= '<tr><td></td><td></td></tr>';
		$obsah .= '<tr><td>Počet akcí</td><td>'.$pocetakci.'</td></tr>';
		$obsah .= '<tr><td>Agent</td><td>'.$agent.'</td></tr>';
		$obsah .= '</table>';
		$xclass = 1;

		if ($event_query->have_posts()) {
			while ($event_query->have_posts()) :
			
					if (($xclass % 5) == 0) {
						$obsah .= '<div class="page_break_now"></div>';
					}
					
					$event_query->the_post();
					$event_location = get_the_terms(get_the_ID(), 'event_location' );
					$event_date_time_raw = get_post_meta(get_the_ID(), 'event_date_time', true);
					$phpdate = date_create($event_date_time_raw);
					$event_date_formatted = date_format($phpdate,"d.m.Y");
					$event_time_formatted = date_format($phpdate,"H:i");
					
					$datum = $event_date_formatted.' - '.$event_time_formatted;
					
					$obsah .= '<table class="exporttable artist">';
					$obsah .= '<tr><td><strong>Akce</strong></td><td>'.get_the_title(get_the_ID()).'</td></tr>';
					$obsah .= '<tr><td><strong>Datum</strong></td><td>'.$datum.'</td></tr>';
					
					foreach ($event_location as $loc) {
						$locid = $loc->term_id;
						$event_lokace_adresa = get_term_meta($locid, 'tc_adresa_zarizeni', true);
						$map_link = 'https://www.google.com/maps/?q='.$event_lokace_adresa .'&key=AIzaSyCxomSUsyB8XoMVJQ3L3dA-CPIzVQRaLHE&language=cs';
						$obsah .= '<tr><td><strong>Místo</strong></td><td>'.$loc->name.'</td></tr>';; 
						$obsah .= '<tr><td>Adresa</td><td><a href="'.$map_link.'">'.$event_lokace_adresa.'</a></td></tr>';; 
						$obsah .= '<tr><td>Jméno</td><td>'.get_term_meta($locid, 'tc_produkce-jmeno', true).'</td></tr>';; 
						$obsah .= '<tr><td>Telefon</td><td>'.get_term_meta($locid, 'tc_produkce-tel', true).'</td></tr>';; 
						$obsah .= '<tr><td>Email</td><td>'.get_term_meta($locid, 'tc_produkce-email', true).'</td></tr>';; 
					} 
					
					$obsah .= '</table>';
					$xclass ++;
				
			endwhile;	
		}
			
	$obsah .= '</div></body></html>';
	
	
	////////////// CREATE PDF //////////////////
	
	require_once 'dompdf/autoload.inc.php';
	
	//use Dompdf\Dompdf;
	$jmeno = $slug.'.pdf';
	$html = mb_convert_encoding($obsah, 'HTML-ENTITIES', 'UTF-8');
		
	$dompdf = new Dompdf($options);
	$dompdf->loadHtml(html_entity_decode($html));
	$dompdf->setPaper('A4', 'portrait');
	
	ob_end_clean();
	$dompdf->render();
	$dompdf->stream($jmeno);
	
}
add_shortcode('export_artist_pdf', 'tc_export_artist_pdf');

?>