<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<base href="{$module_root}/"/>

		<title>DASe Audio Module</title>

		<link rel="stylesheet" type="text/css" href="css/default.css"/>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
		<script type="text/javascript" src="js/javascript.js"></script>
	</head>
	<body>
		<a href="{$app_root}login/{$request->user->eid}" class="delete" id="logoff-link">logout {$request->user->eid}</a>
		<div class="container">

			<div class="ss" id="jpId"></div>

			<h1>{$c->collection_name} Audio</h1>

			<div id="jquery_jplayer"></div>
			<div class="interface">
				<ul class="controls">  
					<li id="play_button">Play</li>  
					<li id="pause_button">Pause</li>   
				</ul>
				<div class="progress">
					<div id="load_bar" class="jp-load-bar">
						<div id="play_bar" class="jp-play-bar"></div>
					</div>
				</div>
				<div id="volume">
					<p>Volume</p>
					<div id="volume_bar" class="jp-volume-bar">
						<div id="volume_bar_value" class="jp-volume-bar-value"></div>
					</div>
				</div>
				<ul id="times">
					<li id="play_time" class="jp-play-time">00:00</li>
					<li  id="total_time" class="jp-total-time">03:09</li>
				</ul>
			</div>
			<div id="title"></div>

			<ul id="files">
				{foreach item=item from=$items}
				<li><a href="{$item.app_root}{$item.enclosure.href}" class="nogo">{$item.metadata.title[0]}</a> <a href="#" class="play">[play]</a></li>
				{/foreach}
			</ul>
			<div class="spacer"></div>
		</div>
	</body>
</html>
