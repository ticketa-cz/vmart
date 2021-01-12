<?php
/*
Plugin Name: Tickera Location Fields
Description: Tickera custom fields for location category
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define('TICKETALOC_PATH', plugin_dir_path( __FILE__ ) );

//// init ////
function init_ticketa_locations() {
	
	include ( TICKETALOC_PATH . "/includes/export_artist_pdf.php");
	include ( TICKETALOC_PATH . "/includes/export_daily_events.php");
	include ( TICKETALOC_PATH . "/includes/export_location_pdf.php");
	
}
add_action( 'plugins_loaded', 'init_ticketa_locations');

//include the main class file

require_once("includes/Tax-meta-class/Tax-meta-class.php");

if (is_admin()){
	
	/* prefix of meta keys, optional */
	$prefix = 'tc_';
	
	/* 
	* configure your meta box
	*/
	$location_config = array(
	'id' => 'location_meta_box',          // meta box id, unique per meta box
	'title' => 'Location Meta Box',          // meta box title
	'pages' => array('event_location'),        // taxonomy name, accept categories, post_tag and custom taxonomies
	'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
	'fields' => array(),            // list of meta fields (can be added by field arrays)
	'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	//add_filter( 'tc_event_fields', 'tc_adresa_zarizeni', 10, 1 );
	/*
	* Initiate your meta box
	*/
	$location_meta = new Tax_Meta_Class($location_config);
	
	//adresy
	$location_meta->addText($prefix.'mesto',array('name'=> __('Město ','tax-meta'),'desc' => 'zadejte město kde se vyskytuje zařízení'));
	$location_meta->addText($prefix.'adresa_zarizeni',array('name'=> __('Adresa zařízení ','tax-meta'),'desc' => 'zadejte adresu zařízení - ulice s čp, psč'));
	$location_meta->addText($prefix.'adresa_korespondencni',array('name'=> __('Adresa korespondenční ','tax-meta'),'desc' => 'zadejte adresu korespondenční - ulice s čp, psč, město'));
	$location_meta->addText($prefix.'ico',array('name'=> __('IČO ','tax-meta')));
	$location_meta->addText($prefix.'zastupitel',array('name'=> __('Zastupitel ','tax-meta')));
	
	//republika
	$location_meta->addRadio($prefix.'republika',array('cr'=>'Česká Republika','sk'=>'Slovensko'),array('name'=> __('Země','tax-meta'), 'std'=> array('cr')));
	//typ prostoru
	$location_meta->addCheckbox($prefix.'typ_divadlo',array('name'=> __('Divadlo ','tax-meta')));
	$location_meta->addCheckbox($prefix.'typ_kd',array('name'=> __('Kulturní dům ','tax-meta')));
	$location_meta->addCheckbox($prefix.'typ_kino',array('name'=> __('Kino ','tax-meta')));
	$location_meta->addCheckbox($prefix.'typ_klub',array('name'=> __('Klub ','tax-meta')));
	$location_meta->addCheckbox($prefix.'typ_festival',array('name'=> __('Festival ','tax-meta')));
	$location_meta->addCheckbox($prefix.'typ_slavnosti',array('name'=> __('Slavnosti ','tax-meta')));
	$location_meta->addCheckbox($prefix.'typ_jine',array('name'=> __('Jiné ','tax-meta')));
	//www
	$location_meta->addText($prefix.'web',array('name'=> __('Web ','tax-meta')));
	//kontakt produkce
	$location_meta->addText($prefix.'produkce-jmeno',array('name'=> __('Kontakt 1 - Jméno ','tax-meta')));
	$location_meta->addText($prefix.'produkce-tel',array('name'=> __('Kontakt 1 - Telefon ','tax-meta')));
	$location_meta->addText($prefix.'produkce-email',array('name'=> __('Kontakt 1 - Email ','tax-meta')));
	//kontakt programove oddeleni
	$location_meta->addText($prefix.'program-jmeno',array('name'=> __('Kontakt 2 - Jméno ','tax-meta')));
	$location_meta->addText($prefix.'program-tel',array('name'=> __('Kontakt 2 - Telefon ','tax-meta')));
	$location_meta->addText($prefix.'program-email',array('name'=> __('Kontakt 2 - Email ','tax-meta')));
	//kontakt technika
	$location_meta->addText($prefix.'technika-jmeno',array('name'=> __('Kontakt 3 - Jméno ','tax-meta')));
	$location_meta->addText($prefix.'technika-tel',array('name'=> __('Kontakt 3 - Telefon ','tax-meta')));
	$location_meta->addText($prefix.'technika-email',array('name'=> __('Kontakt 3 - Email ','tax-meta')));
	
	//sal 1
	$location_meta->addText($prefix.'sal_1_jmeno',array('name'=> __('Jméno sálu 1 ','tax-meta')));
	$location_meta->addText($prefix.'sal_1_kapacita',array('name'=> __('Kapacita sálu 1 ','tax-meta')));
	$location_meta->addFile($prefix.'sal_1_mapa',array('name'=> __('Mapa sálu 1 ','tax-meta')));
	$location_meta->addText($prefix.'sal_1_rozmery_jeviste',array('name'=> __('Rozměry jeviště sálu 1 ','tax-meta'),'desc' => 'šířka, výška, hloubka'));
	$location_meta->addFile($prefix.'sal_1_mapa_jeviste',array('name'=> __('Mapa jeviště sálu 1 ','tax-meta')));
	$location_meta->addText($prefix.'sal_1_pocet_tahu',array('name'=> __('Počet tahů sálu 1 ','tax-meta')));
	
	//sal 2
	$location_meta->addText($prefix.'sal_2_jmeno',array('name'=> __('Jméno sálu 2 ','tax-meta')));
	$location_meta->addText($prefix.'sal_2_kapacita',array('name'=> __('Kapacita sálu 2 ','tax-meta')));
	$location_meta->addFile($prefix.'sal_2_mapa',array('name'=> __('Mapa sálu 2 ','tax-meta')));
	$location_meta->addText($prefix.'sal_2_rozmery_jeviste',array('name'=> __('Rozměry jeviště sálu 2 ','tax-meta'),'desc' => 'šířka, výška, hloubka'));
	$location_meta->addFile($prefix.'sal_2_mapa_jeviste',array('name'=> __('Mapa jeviště sálu 2 ','tax-meta')));
	$location_meta->addText($prefix.'sal_2_pocet_tahu',array('name'=> __('Počet tahů sálu 2 ','tax-meta')));
	
	//sal 3
	$location_meta->addText($prefix.'sal_3_jmeno',array('name'=> __('Jméno sálu 3 ','tax-meta')));
	$location_meta->addText($prefix.'sal_3_kapacita',array('name'=> __('Kapacita sálu 3 ','tax-meta')));
	$location_meta->addFile($prefix.'sal_3_mapa',array('name'=> __('Mapa sálu 3 ','tax-meta')));
	$location_meta->addText($prefix.'sal_3_rozmery_jeviste',array('name'=> __('Rozměry jeviště sálu 3 ','tax-meta'),'desc' => 'šířka, výška, hloubka'));
	$location_meta->addFile($prefix.'sal_3_mapa_jeviste',array('name'=> __('Mapa jeviště sálu 3 ','tax-meta')));
	$location_meta->addText($prefix.'sal_3_pocet_tahu',array('name'=> __('Počet tahů sálu 3 ','tax-meta')));   
	
	$location_meta->Finish();

}

///// NASTAVENI LIST TABLE COLUMNS ///////

function add_event_location_columns( $columns ) {
	
	$columns['tc_ico'] = __( 'IČO', 'tax-meta' );
    return $columns;
	
}
add_filter( 'manage_edit-event_location_columns', 'add_event_location_columns' );

function edit_event_location_columns($content, $column_name, $term_id){
    switch ($column_name) {
        case 'tc_ico':
            $content = get_term_meta( $term_id, 'tc_ico', true );
            break;
        default:
            break;
    }
    return $content;
}
add_filter('manage_event_location_custom_column', 'edit_event_location_columns',10,3);


///////////////// LOCATION LIST ////////////////////

function tc_show_location_list($atts) {
    ob_start();
    extract(shortcode_atts(array(
        'slug' => false,
                    ), $atts)
    );

?>
    <div class="bootstrap-iso kalendar-form">
    <form action="<?php $_PHP_SELF ?>" method="GET" id="locationform">
         
       <div class="form-mesto">
       <label for="mesto">Vyhledat město</label>
       <input type="text" id="mesto" name="mesto" value="<?php if($_GET["mesto"]) { echo $_GET["mesto"]; }?>" >
       </div>
       
       <div class="form-hledat">
       <input type="submit" value="Vyhledat" />
       <input type="submit" value="Vymazat filtr" onClick="document.getElementById('mesto').value = ''" />
       </div>

    </form>
    </div>

    <div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view program-view">
    <div class="table pripravujeme">
    
        <div class="table-row thr">
            <div class="table-cell head">Místo</div>
            <div class="table-cell head">Město</div>
            <div class="table-cell head">Adresa</div>
            <div class="table-cell head">Počet akcí</div>
            <div class="table-cell head">Export PDF</div>
        </div>

    <?php

	if($_GET["mesto"]) {
		$locargs = array(
			'hide_empty' => true,
			'meta_query' => array(
				array(
				   'key'       => 'tc_mesto',
				   'value'     => $_GET["mesto"],
				   'compare'   => 'LIKE'
				)
			),
			'taxonomy'  => 'event_location',
		);
	} else {
		$locargs = array(
			'hide_empty' => true,
			'taxonomy'  => 'event_location',
		);
	}
	$locations = get_terms( $locargs );
	

    if ( ! empty( $locations ) && is_array( $locations ) ) {
        foreach ($locations as $loc) { 

        $location_url_umelec = site_url().'/export_location_pdf.php?loc_id='.$loc->term_id.'&typ=umelec';
		$location_url_technik = site_url().'/export_location_pdf.php?loc_id='.$loc->term_id.'&typ=technik';
		$location_url_agent = site_url().'/export_location_pdf.php?loc_id='.$loc->term_id.'&typ=agent';
        $location_name = $loc->name;
        $location_mesto = get_term_meta($loc->term_id, 'tc_mesto');
        $location_akce = $loc->count;
        $location_adresa = get_term_meta($loc->term_id, 'tc_adresa_zarizeni');


        //if($_GET["mesto"]) {
         //   if ($GET["mesto"] != $location_mesto) {
        ?>
                <div class="table-row">
                    <div class="table-cell"><strong><?php echo $location_name ?></strong></div>
                    <div class="table-cell"><?php echo $location_mesto[0]; ?></div>
                    <div class="table-cell"><?php echo $location_adresa[0]; ?></div>
                    <div class="table-cell"><?php echo $location_akce; ?></div>
                    <div class="table-cell">
                        <a href="<?php echo $location_url_umelec; ?>" class="exportpdf umelec"><i aria-hidden="true" class="fas fa-guitar"></i></a>
                        <a href="<?php echo $location_url_technik; ?>" class="exportpdf technik"><i aria-hidden="true" class="fas fa-cogs"></i></a>
                        <a href="<?php echo $location_url_agent; ?>" class="exportpdf agent"><i aria-hidden="true" class="far fa-id-card"></i></a>
                    </div>
                </div>

        <?php 
          //  }
        //} 

        };
   };
	
    /* Restore original Post Data */
    wp_reset_postdata();
    $content = ob_get_clean();

    return $content;

}

add_shortcode('tc_location_list', 'tc_show_location_list');


//////////// SEZNAM UMELCU /////////////////

function tc_show_artist_list($atts) {
    ob_start();
    extract(shortcode_atts(array(
        'slug' => false,
                    ), $atts)
    );

	?>
    <div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view program-view">
    <div class="table pripravujeme">
    
        <div class="table-row thr">
            <div class="table-cell head">Umělec / Představení</div>
            <div class="table-cell head">Počet akcí</div>
            <div class="table-cell head">Export PDF</div>
        </div>

    <?php
	/// get artists ///
    $artists = get_terms( array (
        'taxonomy' => 'event_category',
        'hide_empty' => true,
    ));

    if ( ! empty( $artists ) && is_array( $artists ) ) {
        foreach ($artists as $art) { 
		
		/// get events ///
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
								'terms' => $art->term_id,
							)
		);
		$event_query = new WP_Query($args);

        $artist_url_umelec = site_url().'/export_artist_pdf.php?art_id='.$art->term_id;
        $artist_name = $art->name;
        $artist_akce = $event_query->post_count;
		
		if ($artist_akce > 0) {
        ?>
                                <div class="table-row">
                                    <div class="table-cell"><strong><?php echo $artist_name ?></strong></div>
                                    <div class="table-cell" align="center"><?php echo $artist_akce; ?></div>
                                    <div class="table-cell" align="center">
										<a href="<?php echo $artist_url_umelec; ?>" class="exportpdf umelec"><i aria-hidden="true" class="fas fa-guitar"></i></a>
                                    </div>
                                </div>
        <?php
		}
        };
   };
	
    /* Restore original Post Data */
    wp_reset_postdata();
    $content = ob_get_clean();
    return $content;
}

add_shortcode('tc_artist_list', 'tc_show_artist_list');
?>