<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<base href="{$module_root}"/>

		<title>DASe Local</title>

		<link rel="stylesheet" type="text/css" href="css/default.css"/>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/app.js"></script>
	</head>
	<body>
		<div class="container">
			<ul id="files">
				{foreach item=item from=$items}
				<li>
				<a href="item/{$c->ascii_id}/{$item.serial_number}">
					<img src="../../media/{$c->ascii_id}/thumbnail/{$item.serial_number}_100.jpg">
				</a>
				<a href="item/{$c->ascii_id}/{$item.serial_number}">{$item.metadata.title[0]}</a>
				</li>
				{/foreach}
			</ul>
			<div class="spacer"></div>
		</div>
	</body>
</html>
