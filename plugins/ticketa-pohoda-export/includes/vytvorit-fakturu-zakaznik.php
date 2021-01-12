<?php
//header('Content-Type: text/html;  charset=Windows-1250');
date_default_timezone_set('Europe/Prague');
	
function exportuj_fakturu_zakaznik( $order_id ) {
		
	if (!$order_id) {
		return;
	}

	include_once ( LISTENKAPOH_PATH . '/includes/faktura-headers.php' );
	include_once ( LISTENKAPOH_PATH . '/includes/faktura-items.php' );
   
	$dom = $xml_header;
	
	$order = wc_get_order( $order_id );
	$order_data = $order->get_data();
	$order_number = $order->get_order_number();
	
	//// customer data ////
		 
	$order_paid     =  date('Y-m-d');
	$order_date     =  date('Y-m-d', strtotime($order_paid. ' + 6 days'));
	$order_due      =  date('Y-m-d', strtotime($order_paid. ' + 9 days'));
	$order_first    =  $order_data['billing']['first_name'];
	$order_first	=  iconv( "UTF-8", "Windows-1250", $order_first);
	$order_last     =  $order_data['billing']['last_name'];
	$order_last	 	=  iconv( "UTF-8", "Windows-1250", $order_last);
	$order_address  =  $order_data['billing']['address_1'] . ' - ' . $order_data['billing']['address_2'];
	$order_address  =  iconv( "UTF-8", "Windows-1250", $order_address);
	$order_city     =  $order_data['billing']['city']; 
	$order_city  	=  iconv( "UTF-8", "Windows-1250", $order_city);
	$order_postcode =  $order_data['billing']['postcode'];
	$order_total    =  intval($order_data['total']);
	$price_with_tax =  get_post_meta($order_id, '_prices_include_tax', true);
	if ($price_with_tax = 'yes') {
		$order_tax  =  intval(0.10 * $order_total);
		$order_total_with_tax = $order_total;
	} else {
		$order_tax  =  intval($order_data['total_tax']);
		$order_total_with_tax = $order_total + $order_tax;
	}
	$order_total_without_tax	=  $order_total - $order_tax;

	$invoice_header_text = iconv( "UTF-8", "Windows-1250",  esc_html(__( 'Invoice for event tickets', 'lstpoh' )) );
	
	//// content ////
			
	$dom .= $xml_invoice_start.'
				<inv:invoiceType>issuedInvoice</inv:invoiceType>
				<inv:number>
					<typ:numberRequested>'.$order_number.'</typ:numberRequested>
				</inv:number>
				<inv:symVar>'.$order_number.'</inv:symVar>
				<inv:date>'.$order_date.'</inv:date>
				<inv:dateTax>'.$order_paid.'</inv:dateTax>
				<inv:dateAccounting>'.$order_paid.'</inv:dateAccounting>
				<inv:dateDue>'.$order_due.'</inv:dateDue>
				<inv:accounting>
					<typ:ids>VST</typ:ids>
				</inv:accounting>
				<inv:classificationVAT>
					<typ:ids>UDA5</typ:ids>
				</inv:classificationVAT>
				<inv:text>'.$invoice_header_text.'</inv:text>
				<inv:partnerIdentity>
					<typ:address>
						<typ:company>'.$order_first.' '.$order_last.'</typ:company>
						<typ:city>'.$order_city.'</typ:city>
						<typ:street>'.$order_address.'</typ:street>
						<typ:zip>'.$order_postcode.'</typ:zip>
						<typ:ico></typ:ico>
						<typ:dic></typ:dic>
					</typ:address>
					<typ:shipToAddress>
						<typ:company></typ:company>
						<typ:city></typ:city>
						<typ:street></typ:street>
					</typ:shipToAddress>
				</inv:partnerIdentity>
				<inv:myIdentity>
					<typ:address>
						<typ:company>VM ART production s.r.o.</typ:company>
						<typ:title>s.r.o.</typ:title>
						<typ:city>Praha 5</typ:city>
						<typ:street>Duškova</typ:street>
						<typ:number>1041/20</typ:number>
						<typ:zip>150 00</typ:zip>
						<typ:ico>06178138</typ:ico>
						<typ:dic>CZ06178138</typ:dic>
						<typ:mobilPhone>602 249 352</typ:mobilPhone>
						<typ:email>produkce@vm-art.cz</typ:email>
						<typ:www>www.vm-art.cz</typ:www>
					</typ:address>
				</inv:myIdentity>
				<inv:paymentType>
					<typ:ids>Plat.kartou</typ:ids>
					<typ:paymentType>draft</typ:paymentType>
				</inv:paymentType>
				<inv:account>
					<typ:ids>ÈS</typ:ids>
					<typ:accountNo>4807129399</typ:accountNo>
				</inv:account>
				<inv:symConst>0308</inv:symConst>
				<inv:liquidation>
					<typ:amountHome>'.$order_total.'</typ:amountHome>
				</inv:liquidation>
				<inv:markRecord>true</inv:markRecord>
			</inv:invoiceHeader>
			'.$invoice_detail_start.'
				'.get_invoice_items($order).'
			</inv:invoiceDetail>
			'.$invoice_summary_start.'
				<inv:roundingDocument>none</inv:roundingDocument>
				<inv:roundingVAT>none</inv:roundingVAT>
				<inv:typeCalculateVATInclusivePrice>VATNewMethod</inv:typeCalculateVATInclusivePrice>
				<inv:homeCurrency>
					<typ:priceNone>0</typ:priceNone>
					<typ:priceLow>0</typ:priceLow>
					<typ:priceLowVAT>0</typ:priceLowVAT>
					<typ:priceLowSum>0</typ:priceLowSum>
					<typ:priceHigh>0</typ:priceHigh>
					<typ:priceHighVAT>0</typ:priceHighVAT>
					<typ:priceHighSum>0</typ:priceHighSum>
					<typ:price3>'.$order_total_without_tax.'</typ:price3>
					<typ:price3VAT>'.$order_tax.'</typ:price3VAT>
					<typ:price3Sum>'.$order_total_with_tax.'</typ:price3Sum>
					<typ:round>
						<typ:priceRound>0</typ:priceRound>
					</typ:round>
				</inv:homeCurrency>
			</inv:invoiceSummary>
		</inv:invoice>
	</dat:dataPackItem>
</dat:dataPack>';	 
	
	//$dom .= $xml_invoice;
	//$dom .= $xml_footer;
	
	$wp_upload_dir = wp_upload_dir();
	$dir = $wp_upload_dir['basedir'] . "/faktury/" . $order_paid;
	
	if( is_dir($dir) === false ) {
		mkdir($dir);
	}
	
	$filePath = $dir.'/fa-'.$order_number.'.xml';	
	
	file_put_contents($filePath, $dom);
	
	include_once ( LISTENKAPOH_PATH . '/includes/odeslat-fakturu.php' );
}
?>