$(document).ready(function() {
	$('#photo_set').find('a').click(function() {
		var href = $(this).attr('href');
		$.colorbox({
			href:href,
			width: '700px',
			opacity: 0.5,
			onComplete: function() {
				$('#closeColorbox').click(function() {$.colorbox.close();});
			}
		}); 
		return false;
	});
});


$(document).ready(function() {
	$('a#contribute').click(function() {
		var href = $(this).attr('href');
		$.colorbox({
			href:href,
			width: '360px',
			opacity: 0.5,
			onComplete: function() {
				Dase.initDelete('my_photo_set');
				$('#closeColorbox').click(function() {$.colorbox.close();});
			}
		}); 
		return false;
	});
});


