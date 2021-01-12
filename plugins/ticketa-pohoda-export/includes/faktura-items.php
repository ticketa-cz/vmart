<?php
function get_invoice_items($order) {
	
	$invoice_items = '';
	
	// add each ticket individually // 
		
	foreach ($order->get_items() as $item_id => $item) {
		
		$item_name = iconv( "UTF-8", "Windows-1250", $item->get_name()); 
		//array_push($ticket_names, $item_name);
		$ticket_id = $item->get_product_id();
		
		// ciselna rada
		$listenka_class = new Listenka_class;
		$event_id = get_post_meta($ticket_id, '_event_name', true);
		
		// zakazka = organizator // tri pismena + automaticka ciselna rada
		$vendor_id = $listenka_class->get_event_vendor($event_id);
		$vendor_zkratka = get_term_meta( $vendor_id, 'tc_vendor_zkratka', true );
		
		// stredisko = umelec // 10
		$event_artist = get_the_terms( $event_id , 'event_artist' );
		$artist_name = $event_artist[0]->slug;
		$artist_only = explode(  '-', $artist_name );
		$artist_names = array_slice($artist_only, 1);
		$stredisko = strtoupper(substr( implode("-", $artist_names), 0, 10));
		
		// cinnost = mesto // 10
		$event_location = $listenka_class->lst_get_event_locations($event_id);
		$mesto_term = get_term( $event_location['event_mesto'] );
		$mesto = iconv( "UTF-8", "Windows-1250", $mesto_term->name );
		$cinnost = strtoupper(substr($mesto_term->slug, 0, 10));
		
		// event info //
		$event_date_time_raw = get_post_meta($event_id, 'event_date_time', true);
		$date = date_i18n(get_option('date_format'), strtotime($event_date_time_raw));
		$date_conv = iconv( "UTF-8", "Windows-1250", $date );
		$event_name = iconv( "UTF-8", "Windows-1250", get_the_title($event_id) );
		
		// order items //
		$product = $item->get_product();
		$item_price_with_tax = $product->get_price();
		$item_price_without_tax = $item_price_with_tax - (0.10 * $item_price_with_tax);
		$quantity = $item->get_quantity();
		
		// CENU OPRAVIT notax = withtax / 110 * 100 , resp naopak //////////////
		
		$invoice_items .= '<inv:invoiceItem>
				<inv:text>'.$event_name.' - '.$mesto.' - '.$date_conv.'</inv:text>
				<inv:quantity>'.number_format( $quantity,1) .'</inv:quantity>
				<inv:coefficient>1.0</inv:coefficient>
				<inv:payVAT>false</inv:payVAT>
				<inv:rateVAT>third</inv:rateVAT>
				<inv:discountPercentage>0.0</inv:discountPercentage>
				<inv:homeCurrency>
					<typ:unitPrice>'.$item_price_without_tax.'</typ:unitPrice>
					<typ:price>'. $item_price_without_tax * $quantity .'</typ:price>
					<typ:priceVAT>'. 0.10 * $item_price_with_tax * $quantity .'</typ:priceVAT>
					<typ:priceSum>'. $item_price_with_tax * $quantity .'</typ:priceSum>
				</inv:homeCurrency>
				<inv:foreignCurrency>
					<typ:unitPrice>0</typ:unitPrice>
					<typ:price>0</typ:price>
					<typ:priceVAT>0</typ:priceVAT>
					<typ:priceSum>0</typ:priceSum>
				</inv:foreignCurrency>
				<inv:PDP>false</inv:PDP>
				<inv:centre>
					<typ:ids>'.$stredisko.'</typ:ids>
				</inv:centre>
				<inv:activity>
					<typ:ids>'.$cinnost.'</typ:ids>
				</inv:activity>
				<inv:contract>
					<typ:ids>'.$vendor_zkratka.'</typ:ids>
				</inv:contract>
				</inv:invoiceItem>';			
	}
	
	return $invoice_items;
}

?>