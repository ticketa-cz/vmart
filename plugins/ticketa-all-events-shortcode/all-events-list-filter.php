<?php

/*
  Plugin Name: Tickera - All Events List Filtered
  Plugin URI: http://tickera.com/
  Description: List all events on any page or post with [all_events_list_filter] shortcode. List is sorted in the ascending order starting with the first upcoming event. Also, past events are automatically removed from the list.
  Author: Tickera.com
  Author URI: http://tickera.com/
  Version: 1.0
  TextDomain: tc
  Domain Path: /languages/

  Copyright 2016 Tickera (http://tickera.com/)
*/
 

function tc_event_list_filter_query($atts) { 

	// enqueue styles and scripts //

	wp_enqueue_script('calendar_js', plugin_dir_url( __FILE__ ).'assets/calendar.js', false, date("Ymd"), true);
		
	wp_enqueue_style('bootsel_css', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css', false, '1.13.9', null);
	wp_script_add_data( 'bootsel_css', array( 'integrity', 'crossorigin' ) , array( 'sha384-YT6Vh7LpL+LTEi0RVF6MlYgTcoBIji2PmGBbXk3D4So5lw1e64pyuwTtbLOED1Li', 'anonymous' ) );
			
	wp_enqueue_script('bootsel_js', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js', false, '1.13.9', true);
	wp_script_add_data( 'bootsel_js', array( 'integrity', 'crossorigin' ) , array( 'sha384-V2ETvVMpY0zaj0P3nwnqTUqHU19jQInctJYWqgEQE/5vFaU3fWdcMfufqV/8ISD7', 'anonymous' ) );
    
    wp_enqueue_style('bootdate_css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css', false, '1.4.1', null);
	wp_script_add_data( 'bootdate_css', array( 'integrity', 'crossorigin' ) , array( 'sha384-oQPlepmWw0NnzP5Cy8gA9Q3XOJrv+Os+uVsv93hZChsFr2FeEk2at3W50doSLPzu', 'anonymous' ) );
			
	wp_enqueue_script('bootdate_js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js', false, '1.4.1', true);
	wp_script_add_data( 'bootdate_js', array( 'integrity', 'crossorigin' ) , array( 'sha384-aHFhM5aT8aFA9xA6PAeaB8dav8Bc3nF2gDv/DnBl7E6Qhutr42h9VSmf7BXTdugy', 'anonymous' ) );
		
			
	// shortcode atribut pro smlouvy //
	
	$atts = shortcode_atts(
        array(
            'smlouvy' => 'false',
        ), $atts, 'all_events_list_filter' );
	$smlouvy = $atts['smlouvy'];
	
	// url parametry //
	
	if( $_GET["umelec"] ) {
      	$cat_slug = $_GET["umelec"];
   	} else {
		$cat_slug = '';
	}
	
	if( $_GET["datum_od"] ) {	
		$date_od = date('Y-m-d H:i', strtotime($_GET["datum_od"]));
   	} else {
		$date_od = '';
	}
	
	if( $_GET["datum_do"] ) {	
		$date_do = date('Y-m-d H:i', strtotime($_GET["datum_do"] . ' +1 day'));
   	} else {
		$date_do = '';
	}
	
    ob_start();
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'post_type' => 'tc_events',
        'posts_per_page' => 30,
        'paged' => $paged,
        'meta_query' => false,
        'order' => 'ASC',
        'orderby' => 'meta_value',
        'post_status' => 'publish',
		'tax_query' => false
    );

		// add category slug filter
		
		if ($cat_slug) {
			$args["tax_query"] = array(
						array(
							'taxonomy' => 'event_category',
							'field' => 'slug',
							'terms' => $cat_slug,
						)
			);
		};
		
		// add date filter
		
		if ($date_od && $date_do) {
			$args["meta_query"] = array(
					'relation' => 'AND',
					array(
						'key' => 'event_date_time',
						'value' => $date_od,
						'type' => 'DATETIME',
						'compare' => '>='
					),
					array(
						'key' => 'event_date_time',
						'value' => $date_do,
						'type' => 'DATETIME',
						'compare' => '<='
					),
					'orderby' => 'event_date_time',
				);
		} else if ($date_od) {
			$args["meta_query"] = array(
					array(
						'key' => 'event_date_time',
						'value' => $date_od,
						'type' => 'DATETIME',
						'compare' => '>='
					),
					'orderby' => 'event_date_time',
				);
		} else if ($date_do) {
			$args["meta_query"] = array(
					'relation' => 'AND',
					array(
						'key' => 'event_date_time',
						'value' => date('Y-m-d H:i'),
						'type' => 'DATETIME',
						'compare' => '>='
					),
					array(
						'key' => 'event_date_time',
						'value' => $date_do,
						'type' => 'DATETIME',
						'compare' => '<='
					),
					'orderby' => 'event_date_time',
				);
		} else {
			$args["meta_query"] = array(
					array(
						'key' => 'event_date_time',
						'value' => date('Y-m-d H:i'),
						'type' => 'DATETIME',
						'compare' => '>='
					),
					'orderby' => 'event_date_time',
				);
		};
	

    // The Query
	
    $the_query = new WP_Query($args);
	
	
	// Get the categories
	
	$cats = get_terms( array (
		'taxonomy' => 'event_category',
		'hide_empty' => true,
	));

	function check_cat_events($artist_slug) {
		$event_args = array(
			'post_type' => 'tc_events',
			'meta_query' => array(
				array(
					'key' => 'event_date_time',
					'value' => date('Y-m-d H:i'),
					'type' => 'DATETIME',
					'compare' => '>='
				),
			),
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'event_category',
					'field' => 'slug',
					'terms' => $artist_slug,
				)
			)
		);

		$event_query = new WP_Query($event_args);
		
		if ($event_query->have_posts()) {
			return true;
		} else {
			return false;
		}
	}

?>

	<div class="bootstrap-iso kalendar-form">
      <form action="<?php $_PHP_SELF ?>" method="GET">
      	 
         <div class="form-umelec form-cast">
         <label for="umelec">Vyberte umělce</label>
         <select name="umelec" id="umelec">
			<?php 
			if ( $_GET["umelec"] ) { ?>
					<option selected=selected value="<?php echo $_GET["umelec"]; ?>" ></option>
			<?php } else { ?>
					<option value="">...</option>
			<?php };
			
            if ( ! empty( $cats ) && is_array( $cats ) ) {
                 foreach ($cats as $ct) {
					 if (check_cat_events($ct->slug) == true) { ?>
				 		<option value="<?php echo $ct->slug; ?>"><?php echo $ct->name; ?></option>				 
				 <?php };
				 };
            };
            ?>
         </select>
         </div>
         
         <div class="form-od form-cast">
         <label for="datum_od">Datum od</label>
         <input class="form-control" id="datum_od" name="datum_od" type="text" <?php if( $_GET["datum_od"] ) { ?>value="<?php echo $_GET["datum_od"]; ?>"<?php }; ?> />
         </div>
         
         <div class="form-do form-cast">
         <label for="datum_do">Datum do</label>
         <input class="form-control" id="datum_do" name="datum_do" type="text" <?php if( $_GET["datum_do"] ) { ?>value="<?php echo $_GET["datum_do"]; ?>"<?php }; ?>/>
         </div>
         
         <div class="form-hledat">
         <input type="submit" value="Vyhledat" />
       	 <input type="submit" value="Vymazat filtr" onClick="document.getElementById('mesto').value = '';document.getElementById('datum_od').value = '';document.getElementById('datum_do').value = '';document.getElementById('umelec').value = '';" />
         </div>
      </form>
    </div>


<div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view program-view">

		<?php if ($smlouvy == 'true') { ?>
        <div style="margin-bottom: 20px; float: right; padding-right: 56px;">
            <span class="woocommerce"><a href="#" class="button status_yellow deactivated>">VYTVOŘENÁ</a></span>
            <span class="woocommerce"><a href="#" class="button status_orange deactivated>">ODESLANÁ</a></span>
            <span class="woocommerce"><a href="#" class="button status_green deactivated>">PODEPSANÁ</a></span>
        </div>
        <?php } ?>


<div class="table pripravujeme">

    <div class="table-row thr">
        <div class="table-cell head">Datum</div>
        <div class="table-cell head">Čas</div>
        <div class="table-cell head ndlft">Interpret</div>
        <div class="table-cell head ndlft">Lokace</div>
        <?php if ($smlouvy == 'false') { ?>
        	<div class="table-cell head">Vstupenky</div>
        <?php } else { ?>
        	<div class="table-cell head">Vytvořit smlouvu</div>        
        <?php } ?>
    </div>
    
	<?php

    // The Loop
	
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) :
            $the_query->the_post();
			
			$id = get_the_ID();
			$location = get_post_meta($id, 'event_location', true);
			$artisturl = get_post_meta($id, 'tc_artist_url', true);
			$ticketurl = get_post_meta($id, 'tc_external_url', true);
			
			if ($ticketurl && $ticketurl !='v') {
				$parsed = parse_url($ticketurl);
				if (empty($parsed['scheme'])) {
					$ticketurl = 'http://' . ltrim($ticketurl, '/');
				}
			}

            $event_date_time_raw = get_post_meta($id, 'event_date_time', true);
            $event_date_formatted = date(get_option('date_format'), strtotime($event_date_time_raw));
            $event_time_formatted = date(get_option('time_format'), strtotime($event_date_time_raw));
			$event_location = get_the_terms($id, 'event_location' );
			$event_artist = get_the_terms($id, 'event_category' );
			$artid = $event_artist[0]->term_id;
				$event = new TC_Event($id);
            	$ticket_types = $event->get_event_ticket_types();
				$event_url = get_permalink($id);
?>
                                
                <div class="table-row">
                    <div class="table-cell dat"><?php echo $event_date_formatted; ?></div>
                    <div class="table-cell cas"><?php echo $event_time_formatted; ?></div>
                    <div class="table-cell hdr">
                        <a href="<?php echo $artisturl; ?>" class="wpem-event-action-url event-style-color <?php echo $event_type;?>">
                             <div class="wpem-event-title"><h3 class="wpem-heading-text"><?php echo the_title(); ?></h3></div>
                        </a>
                    </div>
                    <div class="table-cell loc"><?php if ($location != '') { echo $location; } else {
                            foreach ($event_location as $eventloc) { echo $eventloc->name . ' '; } }
                            ?></div>
                    <div class="table-cell web">
                    
                    <?php if ($smlouvy == 'false') { 
					
					//// pokud jde o obycejny kalendar ////
					
                        if (count($ticket_types) > 0) { ?>
                             	<span class="woocommerce"><a href="<?php echo $event_url; ?>" class="button">vstupenky</a></span>
                        <?php
                        } else {
								if ($ticketurl) { 
									if ($ticketurl == 'v') { ?>
										<span class="vyprodano">vyprodáno</span>
									<?php } else { ?>
										<a href="<?php echo $ticketurl; ?>" target="_blank" class="koupit">koupit</a>
									<?php }
								}
                        } ?>
                    <?php } else if ($smlouvy == 'true') { 
					
					//// nebo kalendar na tvorbu smluv ////
										
						$location_contract_zajisteni_url = site_url().'/smlouva-o-prodeji/?loc_id='.$event_location[0]->term_id.'&e_id='.$id.'&art_id='.$artid.'&typ=prodej';
						$location_contract_spoluprace_url = site_url().'/smlouva-o-spolupraci/?loc_id='.$event_location[0]->term_id.'&e_id='.$id.'&art_id='.$artid.'&typ=spoluprace';
						$location_contract_pronajem_url = site_url().'/smlouva-o-pronajmu/?loc_id='.$event_location[0]->term_id.'&e_id='.$id.'&art_id='.$artid.'&typ=pronajem';
						$location_contract_technicke_url = site_url().'/smlouva-o-technickem/?loc_id='.$event_location[0]->term_id.'&e_id='.$id.'&art_id='.$artid.'&typ=technicke';
						$id_smlouvy = get_post_meta($id, 'event_contract', true);
						if ($id_smlouvy) {
							$typ_smlouvy = get_post_meta($id_smlouvy, 'contract_typ', true);
							$status_smlouvy = get_post_status($id_smlouvy);
							if ($status_smlouvy == 'publish') { 
								$status_class = 'status_green';
								$pdf_url = get_post_meta($id_smlouvy, 'contract_pdf', true); ?>
								<span class="woocommerce"><a href="<?php echo site_url().'/wp-content/uploads/smlouvy/podepsane/'. $pdf_url; ?>" class="button activated" download>PDF</a></span>
                            <?php
							} else if ($status_smlouvy == 'pending') {
								$status_class = 'status_orange';
							} else  if ($status_smlouvy == 'draft') {
								$status_class = 'status_yellow';
							}
						}
						
						if (!$id_smlouvy or $status_class != 'status_green') { ?>
								<span class="woocommerce"><a href="" class="button deactivated" download>PDF</a></span>							
						<?php } ?>
                        
                    	<span class="woocommerce"><a href="<?php echo $location_contract_zajisteni_url; ?>" class="button <?php if($id_smlouvy && $typ_smlouvy == 'prodej') { echo 'activated '.$status_class; } else if ($id_smlouvy) { echo 'deactivated'; } else { echo 'activated'; } ?>">PR</a></span>
                    	<span class="woocommerce"><a href="<?php echo $location_contract_spoluprace_url; ?>" class="button <?php if($id_smlouvy && $typ_smlouvy == 'spoluprace') { echo 'activated '.$status_class; } else if ($id_smlouvy) { echo 'deactivated'; } else { echo 'activated'; } ?>">SP</a></span>
                    	<span class="woocommerce"><a href="<?php echo $location_contract_pronajem_url; ?>" class="button <?php if($id_smlouvy && $typ_smlouvy == 'pronajem') { echo 'activated '.$status_class; } else if ($id_smlouvy) { echo 'deactivated'; } else { echo 'activated'; } ?>">P</a></span>
                    	<span class="woocommerce"><a href="<?php echo $location_contract_technicke_url; ?>" class="button <?php if($id_smlouvy && $typ_smlouvy == 'technicke') { echo 'activated '.$status_class; } else if ($id_smlouvy) { echo 'deactivated'; } else { echo 'activated'; } ?>">TZ</a></span>

                    <?php } ?>
                                        
                    </div>
                </div>
                
                <script type="text/javascript">
				jQuery(document).ready( function ($) {
					$('.deactivated').click(function(e) {
						e.preventDefault();
					});
				});
				</script>

		<?php
        endwhile;
		?>
		
        </div>
		<nav class="event-manager-pagination">
			<ul class="page-numbers">

		<?php		
        	$total_pages = $the_query->max_num_pages;
        	if ($total_pages > 1) {
                    $current_page = max(1, get_query_var('paged'));
                    echo paginate_links(array(
                        'base' => get_pagenum_link(1) . '%_%',
                        'format' => 'page/%#%',
                        'current' => $current_page,
                        'total' => $total_pages,
                        'prev_text' => __('←'),
                        'next_text' => __('→'),
						'type' => 'list',
						//'before_page_number' => '<li class="page-numbers">',
						//'after_page_number'  => '</li>'
                    ));
             }
			 
			 ?>
			 </ul>
		</nav>
        
	<?php	
	
    } else {
        // if no posts are found you can add message here
    }
	
	?>

    </div>
    <?php

    /* Restore original Post Data */
    wp_reset_postdata();
    $content = ob_get_clean();

    return $content;
}

add_shortcode('all_events_list_filter', 'tc_event_list_filter_query');