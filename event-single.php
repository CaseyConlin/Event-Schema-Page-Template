<?php

function e_schema_em(){
	global $wpdb;
	  $postId = get_the_ID();
	  $eventId = $wpdb->get_var($wpdb->prepare("SELECT event_id FROM {$wpdb->prefix}em_events WHERE post_id = %d", $postId ) );
	  $elocationId = get_post_meta($postId, '_location_id', true);  
	  $elocationPostid = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}em_locations WHERE location_id = %d", $elocationId ) );
	  $eventMetaInfo = get_post_meta($postId);
	  $eventPhone = get_post_meta($elocationPostid, 'phoneNumber', true); 
	  $eLocationinfo = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}em_locations WHERE location_id = %d", $elocationId ) ); 
	  $eTicketinfo = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}em_tickets WHERE event_id = %d", $eventId) ); 
	  $ePerformer = get_post_meta($postId, 'Artist Name', true);

  $event_schema_info = array(
	  "@context"=> "http://schema.org",
	  "@type"=> $eventMetaInfo ['Event Type'][0],
	  "name" => get_the_title(),
	  "description" => get_the_excerpt(),
	  "image" => get_the_post_thumbnail_url(),
	  "url"=> get_permalink(),
	  "startDate"=> $eventMetaInfo['_event_start_date'][0].' '.$eventMetaInfo['_event_start_time'][0],
	  "endDate"=>  $eventMetaInfo['_event_end_date'][0].' '.$eventMetaInfo['_event_end_time'][0],
	  "location"=> array (
		"@type"=> "Place",
		"name"=> $eLocationinfo->location_name,
		"description"=> $eLocationinfo->post_content,
		"url"=> home_url(),
		  "address"=> array(
		    "@type"=> "PostalAddress",
		    "streetAddress"=> $eLocationinfo->location_address,
		    "addressLocality"=> $eLocationinfo->location_town,
		    "postalCode"=> $eLocationinfo->location_postcode,
		    "addressCountry"=> "United States",
		    "telephone"=> $eventPhone,
		    "sameAs"=> home_url(),
		    ),
	     ),
	  "offers"=> array(
 	    "@type"=> "Offer",
 		"priceCurrency"=> "USD",
		"price" =>  number_format($eTicketinfo->ticket_price, 2),
		"validFrom"=> $eTicketinfo->ticket_start,
		"url"=> get_permalink(),
	  )
  );
 
    $performer_data =
	array("performer"=> array(
        "@type" =>"PerformingGroup",
 		"name"=> $eventMetaInfo ['Artist Name'][0],
		"sameAs"=> $eventMetaInfo ['Artist Website'][0],
	));
  
	
	if($ePerformer!=""){ 
		$event_schema_info = array_merge($event_schema_info, $performer_data);
	}
	
	echo '<script type="application/ld+json">';
		echo json_encode($event_schema_info, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	echo '</script>';
}

function event_schema(){
	if (is_singular('event')){
	 e_schema_em();
	}
}

add_action( 'wp_head', 'event_schema', 1);

?>




<?php
/* 
 * Remember that this file is only used if you have chosen to override event pages with formats in your event settings!
 * You can also override the single event page completely in any case (e.g. at a level where you can control sidebars etc.), as described here - http://codex.wordpress.org/Post_Types#Template_Files
 * Your file would be named single-event.php
 */
/*
 * This page displays a single event, called during the the_content filter if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */
global $EM_Event;
/* @var $EM_Event EM_Event */
if( empty($args['id']) ) $args['id'] = rand(); // prevent warnings
$id = esc_attr($args['id']);

?>
<div <?php em_template_classes('view-container'); ?>" id="em-view-<?php echo $id; ?>" data-view="event">
	<div class="<?php em_template_classes('event-single'); ?> em-event-<?php echo esc_attr($EM_Event->event_id); ?>" id="em-event-<?php echo $id; ?>" data-view-id="<?php echo $id; ?>">
		<?php
		echo $EM_Event->output_single();
		?>
	</div>
</div>




	