<!doctype html>
<html lang="en">
	<head>
		<base href="{$app_root}">
		<meta charset="utf-8">
		<meta name="template" content="{$smarty.template}">
		<title>eAnthro Community Lab</title>

		<link rel="stylesheet" type="text/css" href="www/css/base.css" /> 
		<link rel="stylesheet" type="text/css" href="www/css/eanthro.css" /> 
		{block name="head-links"}{/block}
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"></script>
		<!--
		<script src="www/js/jquery.js"></script>
		<script src="www/js/jquery-ui.js"></script>
		-->
		<script src="www/js/script.js"></script>
		{block name="head-scripts"}{/block}

	</head>
	<body>
		<div id="ajaxMsg" class="hide">Loading...</div>
		<div class="header">
			<a href="page/home"><img title="eAnthro" src="file/logo_eanthro_header.png"/></a>
			<div id="login" class="login">
				{if $request->user}
				<a href="login" class="delete">logoff {$request->user->eid}</a>
				{if $request->user->is_admin} |
				<a href="admin">admin</a> 
				{/if}
				{else}
				<a href="login">login</a> 
				{/if}
			</div>
		</div>
		<div id="container">
			<div id="content">
				{if $msg}<h3 class="msg">{$msg}</h3>{/if}
				{block name="content_header"}{/block}
				{block name="content"}default content{/block}
			</div> <!-- end content --> 
		</div>
		<div class="footer">
			<div class="first_footer_section">
				<ul>
					<!--
					<li><h2><a href="faq">Frequently Asked Questions</a></h2></li>
					-->
					<li><h2><a href="credits">e-Fossils Production Credits</a></h2></li>
					{foreach item=menu_item from=$request->footer.items}
					{if 'left' == $menu_item.meta2}
					<li><a href="{$menu_item.meta1}"><img src="{$menu_item.links.file}"></a></li>
					{/if}
					{/foreach}
				</ul>
			</div>
			<div class="middle_footer_section">
				<h2>{$request->footer.items.eanthro_labs.title}</h2>
				{$request->footer.items.eanthro_labs.body|markdown}
			</div>
			<div class="last_footer_section">
				<h2>e-Anthro Digital Libraries List</h2>
				<ul class="footer_images">
					{foreach item=menu_item from=$request->footer.items}
					{if 'right' == $menu_item.meta2}
					<li><a href="{$menu_item.meta1}"><img src="{$menu_item.links.file}"></a></li>
					{/if}
					{/foreach}
				</ul>
			</div>
		</div> <!-- end footer -->

	</body>
</html>
