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
			<a href="{$coll_ascii}">return to list</a>
			<dl>
				{foreach item=meta key=key from=$item.metadata}
				<dt>
				{$key}
				</dt>
				<dd>
				<ul>
					{foreach item=val from=$meta}
					<li>{$val}</li>
					{/foreach}
				</ul>
				</dd>
				{/foreach}
			</dl>
			<h1>images</h1>
			<h2 id="rotateable">rotate: 
				<a href="#" class="360">0 degrees</a> |
				<a href="#" class="90">90 degrees</a> |
				<a href="#" class="180">180 degrees</a> |
				<a href="#" class="270">270 degrees</a> |
			</h2>
			<ul class="images">
				{foreach item=size from=$item.media}
				<li><img src="../..{$size}"></li>
				{/foreach}
			</ul>
			<div class="spacer"></div>
		</div>
	</body>
</html>
