<?php 

//// shortcodes ////

function pridej_poradatele() {
	$loc_id = $_POST['loc_id'];
	$poradatel_ico = get_term_meta($loc_id, 'tc_ico', true);
	$poradatel_info = ares_lookup($poradatel_ico);
	
	if ($poradatel_info['error'] == false) {
		$poradatel = '<p align="center"><b>'.$poradatel_info['spolecnost'].'</b><br/>'.$poradatel_info['adresa'].'<br/>'.$poradatel_info['mesto'].', '.$poradatel_info['psc'].'<br/>IČO: '.$poradatel_info['ico'];
		if (!empty($poradatel_info['dic'])) { 
			$poradatel .= '<br/>DIČ: '.$poradatel_info['dic'].'<br/></p>';
		}
	} else {
		$poradatel = 'informace v aresu nenalezeny';
	}
	
	return $poradatel;
}
add_shortcode('smlouva_poradatel', 'pridej_poradatele');

// logo //

function pridej_logo() {
	
	$logo = get_template_directory_uri().'/images/vmart-logo-cm.png';
	
	$logo_type = pathinfo($logo, PATHINFO_EXTENSION);
	$logo_data = file_get_contents($logo);
	$logo_base64 = 'data:image/jpg;base64,' . base64_encode($logo_data);
	
	return '<img src="' . $logo_base64 . '" width="200" height="85"/>';
	
}
add_shortcode('smlouva_logo', 'pridej_logo');

// podpis //

function pridej_podpis() {
	
	$podpis = get_template_directory_uri().'/images/kindl-podpis.png';
	
	$podpis_type = pathinfo($podpis, PATHINFO_EXTENSION);
	$podpis_data = file_get_contents($podpis);
	$podpis_base64 = 'data:image/jpg;base64,' . base64_encode($podpis_data);
	
	return '<img style="clear: both;" src="' . $podpis_base64 . '" width="300" height="183"/>';
	
}
add_shortcode('smlouva_podpis_vm', 'pridej_podpis');

// podpis poradatel //

function pridej_podpis_por() {
	
	$podpis_pod = get_template_directory_uri().'/images/poradatel-podpis.png';
	
	$podpis_pod_type = pathinfo($podpis_pod, PATHINFO_EXTENSION);
	$podpis_pod_data = file_get_contents($podpis_pod);
	$podpis_pod_base64 = 'data:image/jpg;base64,' . base64_encode($podpis_pod_data);
	
	return '<img style="clear: both;" src="' . $podpis_pod_base64 . '" width="300" height="183"/>';
	
}
add_shortcode('smlouva_podpis_por', 'pridej_podpis_por');

// misto datum cas //

function pridej_misto_datum_cas() {
	
	$loc_id = $_POST['loc_id'];
	$eid = $_POST['e_id'];
	$art_id = $_POST['art_id'];
	$misto = get_term_meta($loc_id, 'tc_adresa_zarizeni', true).' - '.get_term_meta($loc_id, 'tc_zeme', true);
	
	$event_date_time_raw = get_post_meta($eid, 'event_date_time', true);
    $datum = date_i18n("d.m. Y", strtotime($event_date_time_raw));
    $cas = date_i18n("H:i", strtotime($event_date_time_raw));
	
	$info = '<p class="smlouva_info">Místo: '.$misto.'<br/>Den konání: '.$datum.'<br/>Začátek vystoupení od: '.$cas.'</p>';

	return $info;
}
add_shortcode('smlouva_misto_datum_cas', 'pridej_misto_datum_cas');

// zvukovka // 

function pridej_zvukovku() {
	
	$eid = $_POST['e_id'];
	$event_date_time_raw = get_post_meta($eid, 'event_date_time', true);
    $cas = date_i18n("H:i", strtotime($event_date_time_raw) - 10800);
	
	return $cas;

}
add_shortcode('smlouva_zvukovka', 'pridej_zvukovku');

// cena //

function pridej_cenu() {
	
	if (!empty($_POST['cena'])) {
		return $_POST['cena'];
	} else {
		return '';
	}

}
add_shortcode('smlouva_cena', 'pridej_cenu');

// ustanoveni //

function pridej_ujednani() {
	
	if (!empty($_POST['ujednani'])) {
		return '<ul id="ujednani_ol" style="list-style-type: circle;"><li>'.$_POST['ujednani'].'</li></ul>';
	} else {
		return '';
	}

}
add_shortcode('smlouva_ujednani', 'pridej_ujednani');

function pridej_ustanoveni() {
	
	if (!empty($_POST['ujednani'])) {
		return '<ul id="ujednani_ol" style="list-style-type: circle;"><li>'.$_POST['ujednani'].'</li></ul>';
	} else {
		return '';
	}

}
add_shortcode('smlouva_ustanoveni', 'pridej_ustanoveni');

// den podpisu // 

function pridej_den_podpisu() {
	
    $datum = date_i18n("d.m. Y", date());
	
	return $datum;

}
add_shortcode('smlouva_den_podpisu', 'pridej_den_podpisu');

// typy vstupenek //

function pridej_typy_vstupenek() {
	
	if (!empty($_POST['typy_vstupenek'])) {
		return $_POST['typy_vstupenek'];
	} else {
		return '';
	}

}
add_shortcode('smlouva_typy_vstupenek', 'pridej_typy_vstupenek');

// predprodej od //

function pridej_predprodej_od() {
	
	if (!empty($_POST['predprodej_od'])) {
		return $_POST['predprodej_od'];
	} else {
		return '';
	}

}
add_shortcode('smlouva_predprodej_od', 'pridej_predprodej_od');

// predprodej misto / url //

function pridej_predprodej_misto() {
	
	if (!empty($_POST['predprodej_misto'])) {
		return $_POST['predprodej_misto'];
	} else {
		return '';
	}

}
add_shortcode('smlouva_predprodej_misto', 'pridej_predprodej_misto');

// podil vm art //

function pridej_podil_vmart() {
	
	if (!empty($_POST['podil_vmart'])) {
		return $_POST['podil_vmart'];
	} else {
		return '';
	}

}
add_shortcode('smlouva_podil_vmart', 'pridej_podil_vmart');

// podil poradatel //

function pridej_podil_poradatel() {
	
	if (!empty($_POST['podil_vmart'])) {
		return ' a ' . (100 - $_POST['podil_vmart']);
	} else {
		return '';
	}

}
add_shortcode('smlouva_podil_poradatel', 'pridej_podil_poradatel');


//// ares lookup ////

function ares_lookup($ico) {
			
	$url = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi?ico=' . $ico;
	$response = wp_remote_get( $url );
	
	if ( ! is_wp_error( $response ) ) {
						
		$body = wp_remote_retrieve_body($response);
		$xml = simplexml_load_string($body);
	
		if ( $xml ) {
	
			$ns = $xml->getDocNamespaces(); 
			$data = $xml->children($ns['are']);
			$data = $data->children($ns['D'])->VBAS;
	
			if ( $data ) {
	
				$return = array( 'error' => false );
				$return['spolecnost'] = $data->OF->__toString();
				$return['ico'] = $data->ICO->__toString();
				$return['dic'] = $data->DIC->__toString();
	
				$cp_1 = $data->AA->CD->__toString();
				$cp_2 = $data->AA->CO->__toString();
				$cp = ( $cp_2 != "" ? $cp_1."/".$cp_2 : $cp_1 );
				$cp = (empty($cp)?$data->AA->CA->__toString():$cp);
				$return['adresa'] = $data->AA->NU->__toString() . ' ' . $cp;
	
				$return['psc'] = $data->AA->PSC->__toString();
				$return['mesto'] = $data->AA->N->__toString();
					
			} else {
				
				$return = 'nenivaresu';
				
			}
	
		} else {
			$return = 'aresneodpovida';
	
		}
		
	} else {
		$return = 'wpseneumipripojit';
	}
	
	return $return;	
}

?>