<?php

function tc_export_location_pdf() {
	
	ob_start();

	$locid = $_GET["loc_id"];
	$typ = $_GET["typ"];
	$term = get_term( $locid, 'event_location' );
	$title = $term->name;
	$slug = $term->slug;
	$prilohy = array();

	
	$obsah = '<html lang="cs-CZ"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
	$obsah .= '<style>'.file_get_contents('../assets/export.css').'</style></head><body>';
	$obsah .= '<div class="exportdiv"><div class="exportlogo"></div><br/><h3>'.$title.'</h3>';
	
		if ($typ == 'agent') {
			$obsah .= '<table class="exporttable">';
			if (get_term_meta($locid, 'tc_republika', true)) { 
				if (get_term_meta($locid, 'tc_republika', true) == 'cr') { $zeme = 'Česká republika'; } else { $zeme = 'Slovensko'; }
				$obsah .= '<tr><td><strong>Země</strong></td><td>'.$zeme.'</td></tr>';
			}
			$obsah .= '<tr><td><strong>Typ zařízení</strong></td><td>';
			if (get_term_meta($locid, 'tc_typ_divadlo', true)) { $obsah .= 'Divadlo | '; }
			if (get_term_meta($locid, 'tc_typ_kd', true)) { $obsah .= 'Kulturní dům | '; }
			if (get_term_meta($locid, 'tc_typ_kino', true)) { $obsah .= 'Kino | '; }
			if (get_term_meta($locid, 'tc_typ_klub', true)) { $obsah .= 'Klub | '; }
			if (get_term_meta($locid, 'tc_typ_festival', true)) { $obsah .= 'Festival | '; }
			if (get_term_meta($locid, 'tc_typ_jine', true)) { $obsah .= 'Jiné | '; }
			$obsah .= '</td></tr>';
			if (get_term_meta($locid, 'tc_web', true)) { $obsah .= '<tr><td><strong>Web</strong></td><td>'.get_term_meta($locid, 'tc_web', true).'</td></tr>'; }
			$obsah .= '</table>';
		}
				
		$obsah .= '<table class="exporttable"><tr><td><strong>Adresa</strong></td><td>'.get_term_meta($locid, 'tc_adresa_zarizeni', true).'</td></tr>';
		$obsah .= '<tr><td></td><td></td></tr>';
		$obsah .= '<tr><td>Jméno</td><td>'.get_term_meta($locid, 'tc_produkce-jmeno', true).'</td></tr>';
		$obsah .= '<tr><td>Telefon</td><td>'.get_term_meta($locid, 'tc_produkce-tel', true).'</td></tr>';
		$obsah .= '<tr><td>Email</td><td>'.get_term_meta($locid, 'tc_produkce-email', true).'</td></tr>';
			
			if ($typ == 'technik') {
				
				if (get_term_meta($locid, 'tc_technika-jmeno', true)) {
					$obsah .= '<tr><td></td><td></td></tr>';
					$obsah .= '<tr><td>Jméno</td><td>'.get_term_meta($locid, 'tc_technika-jmeno', true).'</td></tr>';
					$obsah .= '<tr><td>Telefon</td><td>'.get_term_meta($locid, 'tc_technika-tel', true).'</td></tr>';
					$obsah .= '<tr><td>Email</td><td>'.get_term_meta($locid, 'tc_technika-email', true).'</td></tr>';	
				} else {
					$obsah .= '<tr><td></td><td></td></tr>';
					$obsah .= '<tr><td></td><td>Jiný kontakt není.</td></tr>';
				}
				
				for ($x = 1; $x <= 3; $x++) {
					if (get_term_meta($locid, 'tc_sal_'.$x.'_jmeno', true)) {
						$obsah .= '</table><table class="exporttable">';
						$obsah .= '<tr><td><strong>Sál '.$x.'</strong></td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_jmeno', true).'</td></tr>';
						$obsah .= '<tr><td>Kapacita</td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_kapacita', true).'</td></tr>';
						$obsah .= '<tr><td>Rozměry jeviště</td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_rozmery_jeviste', true).'</td></tr>';	
						$obsah .= '<tr><td>Počet tahů</td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_pocet_tahu', true).'</td></tr>';	
						
						if (get_term_meta($locid, 'tc_sal_'.$x.'_mapa', true)) {
							array_push($prilohy, get_term_meta($locid, 'tc_sal_'.$x.'_mapa', true));
						}
						if (get_term_meta($locid, 'tc_sal_'.$x.'_mapa_jeviste', true)) {
							array_push($prilohy, get_term_meta($locid, 'tc_sal_'.$x.'_mapa_jeviste', true));
						}

					}
				} 
								
			} 
			
			if ($typ == 'agent') {
				
				if (get_term_meta($locid, 'tc_program-jmeno', true)) {
					$obsah .= '<tr><td></td><td></td></tr>';
					$obsah .= '<tr><td>Jméno</td><td>'.get_term_meta($locid, 'tc_program-jmeno', true).'</td></tr>';
					$obsah .= '<tr><td>Telefon</td><td>'.get_term_meta($locid, 'tc_program-tel', true).'</td></tr>';
					$obsah .= '<tr><td>Email</td><td>'.get_term_meta($locid, 'tc_program-email', true).'</td></tr>';
				}
				if (get_term_meta($locid, 'tc_technika-jmeno', true)) {
					$obsah .= '<tr><td></td><td></td></tr>';
					$obsah .= '<tr><td>Jméno</td><td>'.get_term_meta($locid, 'tc_technika-jmeno', true).'</td></tr>';
					$obsah .= '<tr><td>Telefon</td><td>'.get_term_meta($locid, 'tc_technika-tel', true).'</td></tr>';
					$obsah .= '<tr><td>Email</td><td>'.get_term_meta($locid, 'tc_technika-email', true).'</td></tr>';
				}  else if (!get_term_meta($locid, 'tc_technika-jmeno', true) && !get_term_meta($locid, 'tc_program-jmeno', true)) {
					$obsah .= '<tr><td></td><td></td></tr>';
					$obsah .= '<tr><td></td><td>Jiný kontakt není.</td></tr>';
				}
								
				for ($x = 1; $x <= 3; $x++) {
					if (get_term_meta($locid, 'tc_sal_'.$x.'_jmeno', true)) {
						$obsah .= '</table><table class="exporttable">';
						$obsah .= '<tr><td><strong>Sál '.$x.'</strong></td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_jmeno', true).'</td></tr>';
						$obsah .= '<tr><td>Kapacita</td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_kapacita', true).'</td></tr>';
						$obsah .= '<tr><td>Rozměry jeviště</td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_rozmery_jeviste', true).'</td></tr>';	
						$obsah .= '<tr><td>Počet tahů</td><td>'.get_term_meta($locid, 'tc_sal_'.$x.'_pocet_tahu', true).'</td></tr>';	
						
						if (get_term_meta($locid, 'tc_sal_'.$x.'_mapa', true)) {
							array_push($prilohy, get_term_meta($locid, 'tc_sal_'.$x.'_mapa', true));
						}
						if (get_term_meta($locid, 'tc_sal_'.$x.'_mapa_jeviste', true)) {
							array_push($prilohy, get_term_meta($locid, 'tc_sal_'.$x.'_mapa_jeviste', true));
						}

					}
				}
			}
			
	$obsah .= '</table>';
				
	if ($prilohy) {
				
		foreach ($prilohy as $priloha) {

			$path = $priloha['url'];
			$type = pathinfo($path, PATHINFO_EXTENSION);
						
			if ($type = 'pdf') {
				$fp_pdf = fopen($path, 'rb');
				$img = new imagick(); // [0] can be used to set page number
				$img->setResolution(300,300);
				$img->readImageFile($fp_pdf);
				$img->setImageFormat( "jpg" );
				$img->setImageCompression(imagick::COMPRESSION_JPEG); 
				$img->setImageCompressionQuality(90); 
				$img->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
				$data = $img->getImageBlob();
				$type = 'jpg';
			} else {
				$data = file_get_contents($path);
			}
			
			$base64img = 'data:image/' . $type . ';base64,' . base64_encode($data);
			list($width, $height) = getimagesize($base64img);
			if($width > $height) { $imgclass = 'salimg rotate'; } else { $imgclass = 'salimg'; }
						
			$obsah .= '<div class="page_break">';
			//$obsah .= '<h3">'.$base64img.'</h3>';
			$obsah .= '<img class="'.$imgclass.'" src="'.$base64img.'" /></div>';
			
		}
	}
		
	$obsah .= '</div></body></html>';
	
	print_r($obsah);
	
	
	////////////// CREATE PDF //////////////////
	
	require_once 'dompdf/autoload.inc.php';

	
	// reference the Dompdf namespace
	//use Dompdf\Dompdf;
	//use Dompdf\Options;
	//$options = new Options();
	//$options->set('defaultFont', 'Courier');
	$jmeno = $slug.'-'.$typ.'.pdf';
	$html = mb_convert_encoding($obsah, 'HTML-ENTITIES', 'UTF-8');
		
	$dompdf = new Dompdf($options);
	$dompdf->loadHtml(html_entity_decode($html));
	$dompdf->setPaper('A4', 'portrait');
	
	ob_end_clean();
	$dompdf->render();
	$dompdf->stream($jmeno);
	
}
add_shortcode('export_location_pdf', 'tc_export_location_pdf');

?>