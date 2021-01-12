<?php

//////////// CONTRACT AJAX FUNCTIONS /////////////////

//// contract preview ////

function load_contract_preview() {
	
	$art_id = $_POST['art_id'];
	$eid = $_POST['e_id'];
	$typ = $_POST['typ'];
	
	// load contract from db //
	$event_name = get_the_title($eid);
	$event_date_time_raw = get_post_meta($eid, 'event_date_time', true);
    $event_date = date_i18n("d_m_Y", strtotime($event_date_time_raw));
	$jmeno = $event_name.' - '.$event_date.'.pdf';
	$check_title = get_page_by_title(wp_strip_all_tags($jmeno), 'OBJECT', 'contract');
	if (!empty($check_title) ){ $contract_id = $check_title->ID; }

	$smlouva_obsah = get_term_meta($art_id, 'tcct_smlouva_'.$typ, true);
			
	echo do_shortcode($smlouva_obsah);
	wp_die();
}
add_action('wp_ajax_load_preview', 'load_contract_preview');
add_action('wp_ajax_nopriv_load_preview', 'load_contract_preview');


//// mustr smlouvy o pronajmu ////

function create_pronajem() {
	
	$eid = $_POST['e_id'];
	$event_date_time_raw = get_post_meta($eid, 'event_date_time', true);
    $datum = date_i18n("d.m. Y", strtotime($event_date_time_raw));
    $cas = date_i18n("H:i", strtotime($event_date_time_raw));
	$lokace = get_term($_POST['loc_id']);
	
	$output = '<h1>Objednávkový formulář k pronájmu akce</h1>';
	$output .= '<p>Tímto si závazně objednáváme prostor '.$lokace->name.' za účelem konání této akce:</p>';
	$output .= '<p>Název akce: '.get_the_title($eid).'</p>';
	$output .= '<p>Datum konání: '.$datum.'</p>';
	$output .= '<p></p>';
	$output .= '<p>Požadujeme: '.$_POST['pozadujeme'].'</p>';
	$output .= '<p></p>';
	$output .= '<p>Přístup do prostor od – do: '.$_POST['pristup_do_salu'].'</p>';
	$output .= '<p>Příprava sálu – sezení, vč. očíslování řad a sedadel dle zaslaného plánku: '.$_POST['priprava_salu'].'</p>';
	$output .= '<p>Předprodej vstupenek od: '.$_POST['predprodej_od'].'</p>';
	$output .= '<p>Propagace pořadu: '.$_POST['propagace'].'</p>';
	$output .= '<p>Služby po celou dobu pronájmu: '.$_POST['sluzby'].'</p>';
	$output .= '<p>Pomocníci, stagehands v čase a počtu: '.$_POST['crew'].'</p>';
	$output .= '<p>Pódium o minimálních rozměrech šířka / hloubka / výška: '.$_POST['podium'].'</p>';
	$output .= '<p>Zajištění parkování, SPZ: '.$_POST['spz'].'</p>';
	$output .= '<p>Elektřina: '.$_POST['elektrina'].'</p>';
	$output .= '<p>Další požadavky: '.$_POST['dalsi_pozadavky'].'</p>';
	$output .= '<p>Kontakt technik VM ART: '.$_POST['kontakt_technik'].'</p>';
	$output .= '<p></p>';
	$output .= '<p>Časový harmonogram akce: '.$_POST['harmonogram'].'</p>';
	$output .= '<p></p>';
	if ($_POST['prijezd_technika'] != 'undefined:undefined') {
		$output .= '<p>Příjezd techniky VM ART: '.  $_POST['prijezd_technika'].'</p>';
	}
	if ($_POST['prijezd_soubor'] != 'undefined:undefined') {
		$output .= '<p>Příjezd souboru: '.$_POST['prijezd_soubor'].'</p>';
	}
	if ($_POST['zvukovka'] != 'undefined:undefined') {
		$output .= '<p>Zvuková zkouška: '.$_POST['zvukovka'].'</p>';
	}
	$output .= '<p>Začátek pořadu: '.$cas.'</p>';
	if ($_POST['konec'] != 'undefined:undefined') {
		$output .= '<p>Konec pořadu: '.$_POST['konec'].'</p>';
	}
	if ($_POST['demontaz'] != 'undefined:undefined') {
		$output .= '<p>Demontáž: '.$_POST['demontaz'].'</p>';
	}
	$output .= '<p></p>';
	$output .= '<p>Fakturační údaje zde:</p>';
	$output .= '<p></p>';
	$output .= '<p>VM ART production s.r.o.</br>
				zastoupena jednatelem Michalem Kindlem</br>
				zapsáno u Městského soudu v Praze,</br>
				oddíl C, vložka 277570</br>
				Duškova 1041/20, Praha 5, 150 00</br>
				IČO: 06178138</br>
				DIČ: CZ06178138</p>';
	$output .= '<p></p><p></p>';
	$output .= '<p>Fakturu za pronájem prosím zašlete na tento email: <a href="mailto:ucetni@vm-art.cz">ucetni@vm-art.cz</a></p>';
	$output .= '<p></p>';
	$output .= '<p>Objednavatel, v Praze dne: '.date('d.m. Y').'</p>';
	
	echo $output;
	wp_die();
}

add_action('wp_ajax_load_preview_pronajem', 'create_pronajem');
add_action('wp_ajax_nopriv_load_preview_pronajem', 'create_pronajem');


//// export pdf ////

require_once 'mpdf/vendor/autoload.php';

function create_the_pdf() {
	
	$art_id = $_POST['art_id'];
	$typ = $_POST['typ'];
	$eid = $_POST['e_id'];
	$obsah = $_POST['obsah'];
	$event_name = get_the_title($eid);
	$event_date_time_raw = get_post_meta($eid, 'event_date_time', true);
    $event_date = date_i18n("Y_m_d", strtotime($event_date_time_raw));
	
	// vytvor PDF //
	$jmeno = $event_date.' - '.$event_name.' - smlouva_'.$typ;
	
	$html_body = '<html lang="cs-CZ" style="background: #fff;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	$html_body .= '<style>'.file_get_contents(get_stylesheet_directory_uri().'/style.min.css?ver='.date('H:i:s')).'</style>';
	$html_body .= '<style>'.file_get_contents(get_stylesheet_directory_uri().'/pdf-export.css?ver='.date('H:i:s')).'</style>';
	$html_body .= '</head><body style="background: #fff; width: 100%;"><div id="smlouva_export" class="nahled_smlouvy" style="width: 100%; background: #fff; padding: 50px;">';
	$html_body .= stripslashes($obsah);
	$html_body .= '</div>';
	$html_body .= '</body></html>';
	
	// mpdf //
	$mpdf = new \mPDF();
	//ob_end_clean();
	
	$mpdf->WriteHTML($html_body);
	
	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'] . '/smlouvy/' . sanitize_title($jmeno).'.pdf';
	if (file_exists($file)) {
	   unlink($file);
	}
	$mpdf->output($file, 'F');
	
	file_put_contents($upload_dir['basedir'] . '/smlouvy/' . sanitize_title($jmeno).'.html', $html_body);
	
	$file_url = site_url('wp_content/uploads/smlouvy/'.sanitize_title($jmeno).'.pdf');
	
	// vytvor POST //
	$check_title = get_page_by_title(wp_strip_all_tags($jmeno), 'OBJECT', 'contract');
	
	if (empty($check_title) ){
		$contract_id = wp_insert_post(array (
			   'post_type' => 'contract',
			   'post_title' => wp_strip_all_tags($jmeno),
			   'post_content' => $obsah,
			   'post_author' => get_current_user_id(),
			   'post_status' => 'draft',
			   'comment_status' => 'closed',
			   'ping_status' => 'closed',
			   'post_name' => sanitize_title($jmeno),
			   'post_date' => current_time('mysql'),
			   'post_date_gmt' => get_gmt_from_date(current_time('mysql')),
		));
	} else {
		$contract_id = wp_update_post(array (
			   'ID' => $check_title->ID,
			   'post_type' => 'contract',
			   'post_title' => wp_strip_all_tags($jmeno),
			   'post_content' => $obsah,
			   'post_author' => get_current_user_id(),
			   'post_status' => 'draft',
			   'comment_status' => 'closed',
			   'ping_status' => 'closed',
			   'post_name' => sanitize_title($jmeno),
			   'post_date' => current_time('mysql'),
			   'post_date_gmt' => get_gmt_from_date(current_time('mysql')),
		));
	}
		
	if ($contract_id) {
		update_post_meta($contract_id,'contract_typ',$_POST['typ']);
		update_post_meta($contract_id,'contract_ujednani',$_POST['ujednani']);
		update_post_meta($contract_id,'contract_file',$file_url);
		update_post_meta($eid,'event_contract',$contract_id);	
		if ($typ == 'prodej') {
			update_post_meta($contract_id,'contract_cena',$_POST['cena']);
		}
		if ($typ == 'spoluprace') {
			update_post_meta($contract_id,'contract_ticketing',$_POST['typy_vstupenek']);		
			update_post_meta($contract_id,'contract_predprodej_od',$_POST['predprodej_od']);		
			update_post_meta($contract_id,'contract_predprodej_url',$_POST['predprodej_misto']);		
			update_post_meta($contract_id,'contract_podil_vm',$_POST['podil_vmart']);		
		}
			
	} else {
		$contract_id = 'error';
	}
	
	$saved = array();
	$saved['contract_id'] = $contract_id;
	$saved['file_url'] = $file_url;
	
	// odesli data	
	echo json_encode($saved);
	wp_die();	
}
add_action('wp_ajax_create_pdf', 'create_the_pdf');
add_action('wp_ajax_nopriv_create_pdf', 'create_the_pdf');


//// send contract ////

function send_the_contract() {
	
	$contract_id = $_POST['contract_id'];
	$file_url = $_POST['file_url'];
	$eid = $_POST['e_id'];
	$typ = $_POST['typ'];
	$loc_id = $_POST['loc_id'];
	$email_prijemce = get_term_meta($loc_id, 'tc_produkce-email', true);
	$loc_name = get_term($loc_id)->name;
	$filename = basename($file_url);
	
	// load contract from db //
	$event_name = get_the_title($eid);
	$event_date_time_raw = get_post_meta($eid, 'event_date_time', true);
    $event_date = date_i18n("d.m. Y", strtotime($event_date_time_raw));
	$predmet = $event_name.' - '.$event_date.' - smlouva';
	$odkaz = $_SERVER['HTTP_REFERER'];
	$zprava = '';

	// prodej //
	if ($typ == 'prodej') {
		
		$zprava .= 'Dobrý den,<br/><br/>v příloze zasíláme smlouvu k podpisu k pořadu '.$event_name.' - '.$event_date.'.<br/><br/>Smlouvu prosím podepište, naskenujte a na odkazu níže ji můžete naskenovanou nahrát a uložit, nebo lze odeslat na <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a>';
		$zprava .= '<br/><br/><a href="'.$odkaz.'" target="_blank">Zde nahrajte podepsanou smlouvu.</a>';
		$zprava .= '<br/><br/>Pokud byste s některým smluvním ujednáním nesouhlasili, prosím kontaktujte nás<br/>na <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a> nebo na tel. <a href="tel:420727950785">+420 727 950 785</a>';
	
	// spoluprace //	
	} else if ($typ == 'spoluprace') {
		
		$zprava .= 'Dobrý den,<br/><br/>v příloze zasíláme smlouvu k podpisu k pořadu '.$event_name.' - '.$event_date.'.<br/><br/>Do smlouvy prosím na <a href="'.$odkaz.'" target="_blank">odkazu zde</a> doplňte link na předprodej vstupenek. Poté dejte smlouvu znovu uložit a znovu odeslat (přijde vám znovu na email).<br/><br/>Takto vygenerovanou smlouvu prosím podepište, naskenujte a na odkazu níže ji můžete naskenovanou nahrát a uložit, nebo lze odeslat na mail: <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a>';
		$zprava .= '<br/><br/><a href="'.$odkaz.'" target="_blank">Zde nahrajte podepsanou smlouvu.</a>';
	
	// pronajem //	
	} else if ($typ == 'pronajem') {
		
		$zprava .= 'Vážení,<br/><br/>rádi bychom u Vás poptali pronájem sálu '.$loc_name.' na základě přiloženého formuláře.<br/><br/>Prosím o zaslání cenové nabídky na mail: <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a>';
	
	// technicke //	
	} else if ($typ == 'technicke') {
		
		$zprava .= 'Vážení,<br/><br/>v příloze zasíláme smlouvu o technickém zajištění akce k pořadu '.$event_name.'.<br/><br/>Smlouvu prosím podepište, naskenujte a na odkazu níže ji můžete naskenovanou nahrát a uložit, nebo lze odeslat na mail: <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a>';
		$zprava .= '<br/><br/><a href="'.$odkaz.'" target="_blank">Zde nahrajte podepsanou smlouvu.</a>';
		$zprava .= '<br/><br/>Pokud byste s některým smluvním ujednáním nesouhlasili, prosím kontaktujte<br/>nás na <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a> nebo na tel. <a href="tel:420727950785">+420 727 950 785</a>';
		
	}
	// pozdrav //
	$zprava .= '<br/><br/>Děkujeme a přejeme krásný den!<br/><br/>Barbara Novotná<br/>smluvní vztahy, booking<br/>GSM: <a href="tel:420727950785">+420 727 950 785</a><br/>mail: <a href="mailto:booking@vm-art.cz">booking@vm-art.cz</a>';
	$zprava .= '<br/><br/><img width=150 height=54 src="cid:logo_vm_art">';
	
	
	$headers[] = 'From: VM ART <booking@vm-art.cz>';
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$attachments = array( WP_CONTENT_DIR . '/uploads/smlouvy/' .$filename );
	
	$logofile = WP_CONTENT_DIR . '/plugins/ticketa-contracts/assets/logo_vm_art.png';
	$logoid = 'logo_vm_art';
	$logoname = 'logo_vm_art.png';
	
	global $phpmailer;
	add_action( 'phpmailer_init', function(&$phpmailer)use($logofile,$logoid,$logoname){
		$phpmailer->SMTPKeepAlive = true;
		$phpmailer->AddEmbeddedImage($logofile, $logoid, $logoname);
	});
	 
	$mailResult = false;
	$mailResult = wp_mail( $email_prijemce, $predmet, $zprava, $headers, $attachments );	
		
	if ($mailResult == true) {
		$update_post = wp_update_post(array(
			'post_type' => 'contract',
			'ID' => $contract_id,
			'post_status' => 'pending'
		));
		echo $email_prijemce;
		wp_die();
	} else {
		echo 'error';
		wp_die();
	}
	
}
add_action('wp_ajax_send_contract', 'send_the_contract');
add_action('wp_ajax_nopriv_send_contract', 'send_the_contract');


//// load data ////

function load_the_data() {
	
	$eid = $_POST['e_id'];
	$typ = $_POST['typ'];
	$contract_id = 	get_post_meta($eid,'event_contract',true);
		
	if ($contract_id) {	

		$return_data = array();
		$return_data['existuje'] = 'ano';
		$return_data['contract_id'] = $contract_id;
		$return_data['file_url'] = get_post_meta($contract_id,'contract_file',true);
		$return_data['ujednani'] = get_post_meta($contract_id,'contract_ujednani',true);
		if ($typ == 'prodej') {
			$return_data['cena'] = get_post_meta($contract_id,'contract_cena',true);
		}
		if ($typ == 'spoluprace') {
			$return_data['typy_vstupenek'] = get_post_meta($contract_id,'contract_ticketing',true);		
			$return_data['predprodej_od'] = get_post_meta($contract_id,'contract_predprodej_od',true);		
			$return_data['predprodej_misto'] = get_post_meta($contract_id,'contract_predprodej_url',true);		
			$return_data['podil_vmart'] = get_post_meta($contract_id,'contract_podil_vm',true);		
		}
	} else {
		$return_data['existuje'] = 'ne';
	}
	$return_data['logged'] = is_user_logged_in();
	
	echo json_encode($return_data);
	wp_die();
}
add_action('wp_ajax_load_data', 'load_the_data');
add_action('wp_ajax_nopriv_load_data', 'load_the_data');


//// delete contract ////

function delete_the_pdf() {
	
	$eid = $_POST['e_id'];
	$file_url = $_POST['file_url'];	
		
	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'] . '/smlouvy/' . basename($file_url);
	if (file_exists($file)) {
	   unlink($file);
	}
	
	$contract_id = get_post_meta($eid, 'event_contract', true);
	wp_delete_post( $contract_id, true);
	delete_post_meta($eid, 'event_contract');
	
	echo 'ok';
	wp_die();	
}
add_action('wp_ajax_delete_pdf', 'delete_the_pdf');
add_action('wp_ajax_nopriv_delete_pdf', 'delete_the_pdf');


//// update signed contract ////

function update_the_contract() {
	
	$eid = $_POST['e_id'];
	$contract_pdf = $_POST['contract_pdf'];
	
	$contract_id = get_post_meta($eid, 'event_contract', true);
	update_post_meta($contract_id, 'contract_pdf', $contract_pdf);
	
	if ($contract_id) {
		$update_post = wp_update_post(array(
			'post_type' => 'contract',
			'ID' => $contract_id,
			'post_status' => 'publish'
		));
		echo 'ok';
		wp_die();
	} else {
		echo 'error';
		wp_die();
	}	
}
add_action('wp_ajax_update_contract', 'update_the_contract');
add_action('wp_ajax_nopriv_update_contract', 'update_the_contract');
?>