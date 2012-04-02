$(document).ready(function() {

	$('#logoff-link').click(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('href'),
				'type':'DELETE',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});

	$(document).bind("contextmenu",function(e){
		alert('files cannot be downloaded');
		return false;
	});

	$('a[class=nogo]').click(function(e) {
		alert('files cannot be downloaded');
		return false;
	});

    $('a[class=play]').click(function() {
        var file = $(this).prev().attr('href');
        $("#jquery_jplayer").jPlayer("setFile", file);  
        $("#jquery_jplayer").jPlayer("play");  
        var title = $(this).prev().text();
        $('#title').text(title);
        return false;
        });
    $("#jquery_jplayer").jPlayer({
        ready: function () {
					var url = $('#files a:first').attr('href');
					this.element.jPlayer("setFile", url);
    },
        customCssIds: true  
    });
    
    $("#jquery_jplayer").jPlayer("cssId", "play", "play_button"); // Associates play  
    $("#jquery_jplayer").jPlayer("cssId", "pause", "pause_button"); // Associates pause
    $("#jquery_jplayer").jPlayer("cssId", "loadBar", "load_bar");
    $("#jquery_jplayer").jPlayer("cssId", "playBar", "play_bar");
    $("#jquery_jplayer").jPlayer("cssId", "volumeBar", "volume_bar");
    $("#jquery_jplayer").jPlayer("cssId", "volumeBarValue", "volume_bar_value");
	
	$("#jquery_jplayer").jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {
	  $("#play_time").text($.jPlayer.convertTime(pt)); // Default format of 'mm:ss'
	  $("#total_time").text($.jPlayer.convertTime(tt)); // Default format of 'mm:ss'
	});  
  
    $("#jquery_jplayer").jPlayer("onSoundComplete", function() { // Executed when the mp3 ends  
        this.element.jPlayer("stop"); // Auto-repeat  
    });
});

