<?php

/*
  Plugin Name: Tickera - All Events Widget
  Plugin URI: http://tickera.com/
  Description: List all events on any page or post with [all_events_widget] shortcode. List is sorted in the ascending order starting with the first upcoming event. Also, past events are automatically removed from the list.
  Author: Tickera.com
  Author URI: http://tickera.com/
  Version: 1.0
  TextDomain: tc
  Domain Path: /languages/

  Copyright 2016 Tickera (http://tickera.com/)
 */

function tc_event_widget_query() { 
    ob_start();
    //query argumenets
    $args = array(
        'post_type' => 'tc_events',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'event_date_time',
                'value' => date('Y-m-d H:i'),
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

<div id="event-listing-view" class="wpem-main wpem-event-listings event_listings wpem-event-listing-list-view">

<?php

	$c = 1;
    // The Loop
    if ($the_query->have_posts()) {
        while ($c <= 6) {
            $the_query->the_post();
			
			$location = get_post_meta(get_the_ID(), 'event_location', true);
			$artisturl = get_post_meta(get_the_ID(), 'tc_artist_url', true);
			$ticketurl = get_post_meta(get_the_ID(), 'tc_external_url', true);
			
			if ($ticketurl) {
				$parsed = parse_url($ticketurl);
				if (empty($parsed['scheme'])) {
					$ticketurl = 'http://' . ltrim($ticketurl, '/');
				}
			}

            $event_date_time_raw = get_post_meta(get_the_ID(), 'event_date_time', true);
            $event_date_formatted = date(get_option('date_format'), strtotime($event_date_time_raw));
            $event_time_formatted = date(get_option('time_format'), strtotime($event_date_time_raw));
			$event_location = get_the_terms(get_the_ID(), 'event_location' );
?>
                                
            <div class="wpem-event-box-col wpem-col wpem-col-12 wpem-col-md-6 wpem-col-lg-4 ">
              <div class="wpem-event-layout-wrapper">
              <div class="event_listing wpem-event-layout-wrapper post-1747 type-event_listing status-publish hentry">
            
                  <div class="wpem-event-infomation">
            
                      <div class="wpem-event-details">
            
                        <div class="wpem-event-date-time"><span class="wpem-event-date-time-text"><?php echo $event_date_formatted; ?> - <?php  echo $event_time_formatted; ?></span>
                        </div>
                        
                        <a href="<?php echo $artisturl; ?>" class="wpem-event-action-url event-style-color">
                        <div class="wpem-event-title"><h3 class="wpem-heading-text"><?php echo the_title(); ?></h3></div>
                        </a>
                        
                        <div class="wpem-event-location"><span class="wpem-event-location-text"><?php if ($location != '') { echo $location; } else {
											foreach ($event_location as $eventloc) { echo $eventloc->name . ' '; } }
											?></span></div>
                        
                      </div>
                  </div>   
              </div>
              </div>
            </div>

<?php
		$c++;
        }
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

//tc_event_query

add_shortcode('all_events_widget', 'tc_event_widget_query');