/* http://keith-wood.name/timeEntry.html
   Turkish initialisation for the jQuery time entry extension
   Written by Vural Dinçer */
$(document).ready(function() {
	$.timeEntry.regional['tr'] = {show24Hours: true, separator: ':',
		ampmPrefix: '', ampmNames: ['AM', 'PM'],
		spinnerTexts: ['şu an', 'önceki alan', 'sonraki alan', 'arttır', 'azalt']};
	$.timeEntry.setDefaults($.timeEntry.regional['tr']);
});