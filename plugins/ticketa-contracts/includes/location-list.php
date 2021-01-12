<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//////////// SEZNAM UMELCU /////////////////


function tc_show_location_contract_list($atts) {
    ob_start();
    extract(shortcode_atts(array(
        'slug' => false,
                    ), $atts)
    );

	?>
    <div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view program-view">
    <div class="table pripravujeme">
    
        <div class="table-row thr">
            <div class="table-cell head">Lokace</div>
            <div class="table-cell head">Vytvořit smlouvu o : Zajištění / Spolupráci / Pronájmu </div>
        </div>

    <?php
	/// get locations ///
    $locations = get_terms( array (
        'taxonomy' => 'event_location',
        'hide_empty' => false,
    ));

    if ( ! empty( $locations ) && is_array( $locations ) ) {
        foreach ($locations as $loc) {		

        $location_contract_zajisteni_url = site_url().'/smlouva-o-zajisteni/?loc_id='.$loc->term_id;
        $location_contract_spoluprace_url = site_url().'/smlouva-o-spolupraci/?loc_id='.$loc->term_id;
        $location_contract_pronajem_url = site_url().'/smlouva-o-pronajmu/?loc_id='.$loc->term_id;
        $location_name = $loc->name;
		?>
        
            <div class="table-row">
                <div class="table-cell"><strong><?php echo $location_name ?></strong></div>
                <div class="table-cell" align="center">
                    <a href="<?php echo $location_contract_zajisteni_url; ?>" class="exportpdf umelec"><i aria-hidden="true" class="fas fa-guitar"></i></a>
                    <a href="<?php echo $location_contract_spoluprace_url; ?>" class="exportpdf umelec"><i aria-hidden="true" class="fas fa-guitar"></i></a>
                    <a href="<?php echo $location_contract_pronajem_url; ?>" class="exportpdf umelec"><i aria-hidden="true" class="fas fa-guitar"></i></a>
                </div>
            </div>
            
        <?php
        }
   }
	
    /* Restore original Post Data */
    wp_reset_postdata();
    $content = ob_get_clean();
    return $content;
}

add_shortcode('tc_location_contract_list', 'tc_show_location_contract_list');
?>