$(document).ready(function(){
	$('div.step_thumbnails ul li').click(function(){
		if(!$(this).hasClass('selected')){
			$(this).addClass('selected').siblings('li').removeClass('selected');	
			var step = $(this).index() + 1;
			$('div.step_viewer').children('div').removeClass('selected').filter('div.step'+step).addClass('selected');
		}
	});
});
