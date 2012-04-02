var App = {};

$(document).ready(function() {
	App.initRotate();
});


App.initRotate = function() {
	$('#rotateable').find('a').click(function() {
		if ('360' == $(this).attr('class')) {
			$('img').removeClass();
		}
		if ('90' == $(this).attr('class')) {
			$('img').removeClass();
			$('img').addClass('rotate-90');
		}
		if ('180' == $(this).attr('class')) {
			$('img').removeClass();
			$('img').addClass('rotate-180');
		}
		if ('270' == $(this).attr('class')) {
			$('img').removeClass();
			$('img').addClass('rotate-270');
		}
		App.initRotate();
		return false;
	});
};
