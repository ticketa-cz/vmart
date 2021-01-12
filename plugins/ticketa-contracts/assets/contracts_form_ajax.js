jQuery(document).ready( function ($) {
	
	var pathname = window.location.pathname;
	
	$("#pdf_preview").hide();
		
	if (pathname == '/smlouva-o-pronajmu/') {
		var form_id = 10314;
		load_data(form_id,load_preview_pronajem);
		//load_preview_pronajem(form_id);
	} else if (pathname == '/smlouva-o-prodeji/'){
		var form_id = 10295;
		load_data(form_id,load_preview);
		//load_preview(form_id);
	} else if (pathname == '/smlouva-o-spolupraci/'){
		var form_id = 10312;
		load_data(form_id,load_preview);
		//load_preview(form_id);
	}
		
	var ulozeno = '';
	
	//// clicks ////
	
	$('#obnovit').on('click', function() {

		var pathname = window.location.pathname;
				
		if (pathname == '/smlouva-o-pronajmu/') {
			var form_id = 10314;
			load_preview_pronajem(form_id);
		} else if (pathname == '/smlouva-o-prodeji/'){
			var form_id = 10295;
			load_preview(form_id);
		} else if (pathname == '/smlouva-o-spolupraci/'){
			var form_id = 10312;
			load_preview(form_id);
		}
		ulozeno = '';
	
	});
	
	$('#ulozit').on('click', function() {

		var pathname = window.location.pathname;
				
		if (pathname == '/smlouva-o-pronajmu/') {
			var form_id = 10314;
		} else if (pathname == '/smlouva-o-prodeji/'){
			var form_id = 10295;
		} else if (pathname == '/smlouva-o-spolupraci/'){
			var form_id = 10312;
		}
		create_pdf(form_id);
		ulozeno = '1';
	
	});
	
	$('#smazat').on('click', function() {

		var pathname = window.location.pathname;
				
		if (pathname == '/smlouva-o-pronajmu/') {
			var form_id = 10314;
		} else if (pathname == '/smlouva-o-prodeji/'){
			var form_id = 10295;
		} else if (pathname == '/smlouva-o-spolupraci/'){
			var form_id = 10312;
		}
		
		if (confirm('Opravdu smazat?')) {
			delete_pdf(form_id);
			ulozeno = '';
		}		
	
	});
	
	$('#odeslat').on('click', function() {

		var pathname = window.location.pathname;
				
		if (pathname == '/smlouva-o-pronajmu/') {
			var form_id = 10314;
		} else if (pathname == '/smlouva-o-prodeji/'){
			var form_id = 10295;
		} else if (pathname == '/smlouva-o-spolupraci/'){
			var form_id = 10312;
		}
		if (confirm('Opravdu odeslat?')) {
			send_contract(form_id);
		}
	
	});
	
	$('#submit button').on('click', function() {

		var pathname = window.location.pathname;
		
				
		if (pathname == '/smlouva-o-pronajmu/') {
			var form_id = 10314;
		} else if (pathname == '/smlouva-o-prodeji/'){
			var form_id = 10295;
		} else if (pathname == '/smlouva-o-spolupraci/'){
			var form_id = 10312;
		}
		update_contract(form_id);
	
	});
	
	//// functions ////
	
	function load_data(form_id, callback) {
		
		var form = $( '#forminator-module-' + form_id );
		var typ = getUrlParameter('typ');
		
		$.ajax({
			  type: 'POST',
			  url: contracts_ajax_url,
			  dataType: 'json',
			  data: {
				  'action': 'load_data',
				  'e_id': getUrlParameter('e_id'),
				  'typ': typ,
			  },
			  beforeSend: function() {
					form.addClass('forminator-fields-disabled');
			  },
			  complete: function(){
			  },
			  success: function( data ) {
				  var logged = data.logged;
				  if (data.existuje == 'ano') {					
						$("#file_url").val(data.file_url);
						$("#pdf_preview").toggle().prop("href",data.file_url).prop("download", "");
						$("#contract_id").val(data.contract_id);
						$('textarea[name="textarea-1"').val(data.ujednani);
						if (typ == 'prodej') {
							$('input[name="number-1"]').val(data.cena);
						}
						if (typ == 'spoluprace') {
							$('input[name="text-1"]').val(data.typy_vstupenek);
						    $('input[name="date-1"').val(data.predprodej_od);
						    $('input[name="url-1"').val(data.predprodej_misto);
						    $('input[name="number-1"]').val(data.podil_vmart);
						}
						setTimeout(callback(form_id,logged), 2000);
				  } else {
					    setTimeout(callback(form_id,logged), 2000);
				  }
			  },
			  error: function( errorThrown ) {
				  alert(JSON.stringify(errorThrown));
			  }
		});
		
		
	};
		
	function load_preview(form_id,logged) {
		
		var form = $( '#forminator-module-' + form_id );
		
		$.ajax({
			  type: 'POST',
			  url: contracts_ajax_url,
			  //dataType: 'json',
			  data: {
				  'action': 'load_preview',
				  'e_id': getUrlParameter('e_id'),
				  'loc_id': getUrlParameter('loc_id'),
				  'art_id': getUrlParameter('art_id'),
				  'typ': getUrlParameter('typ'),
				  'cena': $('input[name="number-1"]').val(),
				  'ujednani' : $('textarea[name="textarea-1"').val(),
				  'typy_vstupenek' : $('input[name="text-1"').val(),
				  'predprodej_od' : $('input[name="date-1"').val(),
				  'predprodej_misto' : $('input[name="url-1"').val(),
				  'podil_vmart': $('input[name="number-1"]').val()
			  },
			  beforeSend: function() {
			  },
			  complete: function(){
					form.removeClass('forminator-fields-disabled');
				    if (logged == false) {
						$('.user_disabled input').prop("disabled", true);
						$('.user_disabled').prop("disabled", true);
					}
					if (logged == true) {
						$('.user_disabled input').prop("disabled", false);
						$('.user_disabled').prop("disabled", false);
					}
					//var start_id = +$("#ujednani_ol").prev("ol").attr("start") + 1;
					//$("#ujednani_ol").attr("start", start_id);
					
					//$('td:first-child').addClass('table_podpis');
					$('td:nth-child(2)').addClass('align_right');
					//$('td p:nth-child(1)').addClass('table_podpis_mezera');
					$('p:first').addClass('nadpis_akce');
					
					$("p[align='left']").addClass('align_left').attr("align", "left");
					$("p[align='right']").addClass('align_right').attr("align", "right").css("text-align", "right");
					$("h1[align='center']").addClass('align_center').attr("align", "center");
					$("p[align='center']").addClass('align_center').attr("align", "center");
			  },
			  success: function( data ) {
					$("#nahled").empty();
					$("#nahled").append(data);
			  },
			  error: function( errorThrown ) {
				  alert(JSON.stringify(errorThrown));
			  }
		});
	};
	
	function load_preview_pronajem(form_id) {
		
		var form = $( '#forminator-module-' + form_id );

		$.ajax({
			  type: 'POST',
			  url: contracts_ajax_url,
			  //dataType: 'json',
			  data: {
				  'action': 'load_preview_pronajem',
				  'e_id': getUrlParameter('e_id'),
				  'loc_id': getUrlParameter('loc_id'),
				  'pozadujeme': $('input[name="text-1"]').val(),
				  'pristup_do_salu': $('input[name="text-2"]').val(),
				  'priprava_salu': $('input[name="text-3"]').val(),
				  'predprodej_od': $('input[name="date-1"]').val(),
				  'propagace': $('input[name="text-5"]').val(),
				  'sluzby': $('input[name="text-6"]').val(),
				  'crew': $('input[name="text-7"]').val(),
				  'podium': $('input[name="text-8"]').val(),
				  'spz': $('input[name="text-9"]').val(),
				  'elektrina': $('input[name="text-10"]').val(),
				  'dalsi_pozadavky': $('input[name="text-11"]').val(),
				  'kontakt_technik': $('input[name="phone-1"]').val(),
				  'harmonogram': $('input[name="text-12"]').val(),
				  'prijezd_technika': $('input[name="time-1-hours"]').val()+':'+$('input[name="time-1-minutes"]').val(),
				  'prijezd_soubor': $('input[name="time-2-hours"]').val()+':'+$('input[name="time-2-minutes"]').val(),
				  'zvukovka': $('input[name="time-3-hours"]').val()+':'+$('input[name="time-3-minutes"]').val(),
				  'konec': $('input[name="time-4-hours"]').val()+':'+$('input[name="time-4-minutes"]').val(),
				  'demontaz': $('input[name="time-5-hours"]').val()+':'+$('input[name="time-5-minutes"]').val(),
			  },
			  beforeSend: function() {
					form.addClass('forminator-fields-disabled');
			  },
			  complete: function(){
					form.removeClass('forminator-fields-disabled');
			  },
			  success: function( data ) {
					$("#nahled").empty();
					$("#nahled").append(data);					
			  },
			  error: function( errorThrown ) {
				  alert(JSON.stringify(errorThrown));
			  }
		});
	};
	
	function create_pdf(form_id) {
		
		var form = $( '#forminator-module-' + form_id );
		var typ = getUrlParameter('typ');
		
		if ($('#nahled').html() == '') {
			
			alert('Nejdříve obnovte náhled prosím.');
			
		} else {
		
			$.ajax({
				  type: 'POST',
				  url: contracts_ajax_url,
				  dataType: 'json',
				  data: {
					  'action': 'create_pdf',
					  'e_id': getUrlParameter('e_id'),
					  'loc_id': getUrlParameter('loc_id'),
					  'art_id': getUrlParameter('art_id'),
					  'typ': typ,
					  'ujednani' : $('textarea[name="textarea-1"').val(),
					  'cena': $('input[name="number-1"]').val(),
					  'typy_vstupenek' : $('input[name="text-1"').val(),
					  'predprodej_od' : $('input[name="date-1"').val(),
					  'predprodej_misto' : $('input[name="url-1"').val(),
					  'podil_vmart': $('input[name="number-1"]').val(),
					  'obsah': $('#nahled').html(),
				  },
				  beforeSend: function() {
						form.addClass('forminator-fields-disabled');
				  },
				  complete: function(){
						form.removeClass('forminator-fields-disabled');
				  },
				  success: function( data ) {
						$("#file_url").val(data.file_url);
						$("#contract_id").val(data.contract_id);
						$("#oznameni").html('Smlouva byla uložena.');
				  },
				  error: function( errorThrown ) {
					  alert(JSON.stringify(errorThrown));
				  }
			});
		}
	};
	
	function send_contract(form_id) {
		
		var form = $( '#forminator-module-' + form_id );
		
		if ($('#file_url').val() == '') {
			
			alert('Nejdříve uložte smlouvu prosím.');
			
		} else {
						
			if (Boolean(ulozeno) == false) {
				alert('Nezapomeňte smlouvu před odesláním uložit prosím.');
				return;
			}
		
			$.ajax({
				  type: 'POST',
				  url: contracts_ajax_url,
				  //dataType: 'json',
				  data: {
					  'action': 'send_contract',
					  'e_id': getUrlParameter('e_id'),
					  'typ': getUrlParameter('typ'),
					  'loc_id': getUrlParameter('loc_id'),
					  'contract_id': $('#contract_id').val(),
					  'file_url': $('#file_url').val(),
				  },
				  beforeSend: function() {
						form.addClass('forminator-fields-disabled');
				  },
				  complete: function(){
						form.removeClass('forminator-fields-disabled');
				  },
				  success: function( data ) {
					  if (data != 'error') {
							$("#oznameni").html('Smlouva byla odeslána na email: ' + data);
					  } else {
							$("#oznameni").html('Smlouva se neodeslala.');
					  }
				  },
				  error: function( errorThrown ) {
					  alert(JSON.stringify(errorThrown));
				  }
			});
		}
	};
	
	
	//// delete contract ////
	
	function delete_pdf(form_id) {
		
		var form = $( '#forminator-module-' + form_id );
		
		$.ajax({
			  type: 'POST',
			  url: contracts_ajax_url,
			  data: {
				  'action': 'delete_pdf',
				  'e_id': getUrlParameter('e_id'),
				  'file_url': getUrlParameter('file_url'),
			  },
			  beforeSend: function() {
					form.addClass('forminator-fields-disabled');
			  },
			  complete: function(){
					form.removeClass('forminator-fields-disabled');
			  },
			  success: function( data ) {
				  if (data == 'ok') {					
						$("#oznameni").html('Smlouva byla smazána.');
						$(":input").val('');
				  } else {
					    $("#oznameni").html('Smlouvu se nepodařilo smazat.');
				  }
			  },
			  error: function( errorThrown ) {
				  alert(JSON.stringify(errorThrown));
			  }
		});		
	};
	
	
	//// delete contract ////
	
	function update_contract(form_id) {
		
		if ($(".forminator-file-upload span:first").html() == 'Soubor nebyl vybrán.') {
			alert('Soubor nebyl nahrán.');
			return;
		}
		
		var form = $( '#forminator-module-' + form_id );
		
		$.ajax({
			  type: 'POST',
			  url: contracts_ajax_url,
			  data: {
				  'action': 'update_contract',
				  'e_id': getUrlParameter('e_id'),
				  'contract_pdf': $(".forminator-file-upload span:first").html(),
			  },
			  beforeSend: function() {
					form.addClass('forminator-fields-disabled');
			  },
			  complete: function(){
					form.removeClass('forminator-fields-disabled');
			  },
			  success: function( data ) {
				  if (data != 'ok') {					
						alert('Status smlouvy nebyl aktualizován.');
				  }
			  },
			  error: function( errorThrown ) {
				  alert(JSON.stringify(errorThrown));
			  }
		});		
	};
		
	function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;
	
		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');
	
			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	}
	
});