<?php
/*
  Plugin Name: Tickera - All Events Category
  Plugin URI: http://tickera.com/
  Description: List all events of a category with a shortcode [tc_category_sc slug="category_slug"]
  Author: Tickera.com
  Author URI: http://tickera.com/
  Version: 1.0
  TextDomain: tc
  Domain Path: /languages/

  Copyright 2017 Tickera (http://tickera.com/)
 * IS
 */

function tc_events_by_category_query($atts) {
    ob_start();
    extract(shortcode_atts(array(
        'slug' => false,
                    ), $atts));

    $args = array(
        'post_type' => 'tc_events',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'event_category',
                'field' => 'slug',
                'terms' => $slug,
            ),
        ),
        'meta_query' => array(
            array(
                'key' => 'event_date_time',
                'value' => date('Y-m-d h:i'),
                'type' => 'DATETIME',
                'compare' => '>='
            ),
            'orderby' => 'event_date_time',
        ),
        'order' => 'ASC',
        'orderby' => 'meta_value',
        'post_status' => 'publish'
    );

    // The Query
    $the_query = new WP_Query($args);
	
?>

<div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view program-view">
<div class="table pripravujeme">

    <div class="table-row thr">
        <div class="table-cell head">Datum</div>
        <div class="table-cell head">Čas</div>
        <div class="table-cell head ndlft">Interpret</div>
        <div class="table-cell head ndlft">Lokace</div>
        <div class="table-cell head">Vstupné</div>
    </div>

<?php

    // The Loop
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
			
			$id = get_the_ID();
			$location = get_post_meta($id, 'event_location', true);
			$artisturl = get_post_meta($id, 'tc_artist_url', true);
			$ticketurl = get_post_meta($id, 'tc_external_url', true);
			
			if ($ticketurl) {
				$parsed = parse_url($ticketurl);
				if (empty($parsed['scheme'])) {
					$ticketurl = 'http://' . ltrim($ticketurl, '/');
				}
			}

            $event_date_time_raw = get_post_meta($id, 'event_date_time', true);
            $event_date_formatted = date(get_option('date_format'), strtotime($event_date_time_raw));
            $event_time_formatted = date(get_option('time_format'), strtotime($event_date_time_raw));
			$event_location = get_the_terms($id, 'event_location' );
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
						<?php 
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
                    </div>
                </div>

        <?php
        }
    } else {
        // if no posts are found you can add message here
    }
	
	?>
    </div>
    </div>
    <?php
	
    /* Restore original Post Data */
    wp_reset_postdata();
    $content = ob_get_clean();

    return $content;

}

//tc_event_query

add_shortcode('tc_category_sc', 'tc_events_by_category_query');        