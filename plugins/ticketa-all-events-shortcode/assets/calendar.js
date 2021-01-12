jQuery(document).ready( function ($) {
	$.fn.datepicker.dates['cs'] = {
		days: ["Neděle", "Pondělí", "Úterý", "Středa", "Čtvrtek", "Pátek", "Sobota"],
		daysShort: ["Ned", "Pon", "Úte", "Stř", "Čtv", "Pát", "Sob"],
		daysMin: ["Ne", "Po", "Út", "St", "Čt", "Pá", "So"],
		months: ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"],
		monthsShort: ["Led", "Úno", "Bře", "Dub", "Kvě", "Čer", "Čnc", "Srp", "Zář", "Říj", "Lis", "Pro"],
		today: "Dnes",
		clear: "Vymazat",
		monthsTitle: "Měsíc",
		weekStart: 1,
        format: 'yyyy.mm.dd'
	};
});

jQuery(document).ready( function ($) {
      var date_input1 = $('input[name="datum_od"]'); //our date input has the name "date"
      var container1 = $('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options = {
        format: 'dd.mm.yyyy',
        container: container1,
        todayHighlight: true,
        autoclose: true,
		orientation: "top",
		language: 'cs'		
      };
      date_input1.datepicker(options);
});   
 
jQuery(document).ready( function ($) {
      var date_input2 = $('input[name="datum_do"]'); //our date input has the name "date"
      var container2 = $('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
      var options = {
        format: 'dd.mm.yyyy',
        container: container2,
        todayHighlight: true,
        autoclose: true,
		orientation: "top",
		language: 'cs'
      };
      date_input2.datepicker(options);
});