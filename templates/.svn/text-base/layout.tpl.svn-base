<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		{block name="head-meta"}{/block}
		<title>{block name="title"}DASe: Digital Archive Services{/block}</title>
		<style type="text/css">
			{block name="style"}{/block}
		</style>
		<meta name="description" content="
		The Digital Archive Services project 
		is a lightweight digital content repository
		created by the College of Liberal Arts at 
		The University of Texas at Austin.">

		<base href="{$app_root}">

		<link rel="stylesheet" type="text/css" href="www/css/yui.css">
		<link rel="stylesheet" type="text/css" href="www/css/style.css">
		<link rel="stylesheet" type="text/css" href="www/css/menu.css">
		<link rel="stylesheet" type="text/css" href="www/css/local.css">
		<link rel="shortcut icon" href="www/images/favicon.ico">
		{block name="head-links"}{/block}

		<!-- atompub discovery -->
		<link rel="service" type="application/atomsvc+xml" href="service">
		{block name="servicedoc"}{/block}

		<script type="text/javascript" src="www/js/webtoolkit.base64.js"></script>
		<script type="text/javascript" src="www/js/json2.js"></script>
		<script type="text/javascript" src="www/js/dase.js"></script>
		<script type="text/javascript" src="www/js/dase/htmlbuilder.js"></script>
		{block name="head"}{/block}


		<!--[if lt IE 8]>
		<link rel="stylesheet" type="text/css" href="css/ie.css">
		<![endif]-->

		{if $feed_url}
		<link rel="alternate" type="application/atom+xml" href="{$feed_url}">
		{/if}

		{if $json_url}
		<link rel="alternate" type="application/json" href="{$json_url}">
		{/if}

	</head>

	<body>
		<div id="skipnav"><a href="#maincontent" title="Skip to main content">Skip to main content</a></div>

		<noscript>
			<h1 class="alert">The optimal DASe experience requires Javascript!</h1>
		</noscript>

		{if $page_logo_src}
		<div id="pageLogo">
			<a href="{$page_logo_link_target}"><img src="{$page_logo_src}" alt="page logo"></a>
		</div>
		{/if}

		<div id="header">
			<div id="logoffControl" class="login hide">
				<span id="userName"></span> 
				|
				<a href="settings" class="edit" id="settings-link">settings</a> 
				|
				<a href="logoff" class="edit" id="logoff-link">logout</a>
				<div id="eid" class="pagedata"></div>
			</div>

			<div id="loginControl" class="login hide">
				<div>Got a UT EID? <a href="login/form" class="alert">login!</a></div>
			</div>
			{$main_title}
			<span id="dbName"></span>
		</div>

		<div id="sidebar">
			<ul id="menu" class="hide">
				<li id="home-menu"><a href="" class="main">Home/Search</a></li>
				<li id="cart-menu"><a href="" class="main" id="cartLink">My Cart (<span id="cartCount"></span>)</a></li>
				<li id="sets-menu"><a href="" class="main">My Sets</a>
				<ul class="hide" id="sets-submenu">
					<li>&nbsp;</li>
				</ul>
				</li>
				<li id="shared-menu"><a href="tags" class="main">Public/Shared Sets</a></li>
				<li id="recent-menu"><a href="" class="main">Recently Viewed</a>
				<ul class="hide" id="recent-submenu">
					<li>&nbsp;</li>
				</ul>
				</li>
				<li id="searches-menu"><a href="" class="main">Recent Searches</a>
				<ul class="hide" id="searches-submenu">
					<li>&nbsp;</li>
				</ul>
				</li>
				{if $module_menu}
				{include file="$module_menu"}
				{/if}
			</ul>

			<ul id="menuGrayed">
				<li><a href="" class="main">Home/Search</a></li>
				<li><a href="" class="main">My Cart</a></li>
				<li><a href="" class="main">My Sets</a></li>
				<li><a href="" class="main">Public/Shared Sets</a></li>
				<li><a href="" class="main">Recently Viewed</a></li>
				<li><a href="" class="main">Recent Searches</a></li>
			</ul>

			<h5 id="ajaxMsg" class="hide">loading...</h5>

		</div> <!-- closes sidebar -->

		<div id="content">
			<!-- accessibility -->
			<a id="maincontent" name="maincontent"></a>
			{block name="content"}default_content{/block}
		</div>

		<div class="spacer"></div>

		<div id="footer">
			<a href="manage" class="hide manage" id="manageLink"></a> |
			<a href="help" id="helpModule">FAQ</a> | 
			<a href="mailto:dase@mail.laits.utexas.edu">email</a> | 
			<a href="help">Copyright/Usage Statement</a> | 
			<a href="resources">Resources</a> | 
			<a href="admin" class="hide" id="adminLink"></a> |
			{$request->elapsed} seconds |
			<img src="www/images/dasepowered.png" alt="DASePowered icon">
		</div><!--closes footer-->
		<div id="debugData" class="pagedata"></div>
	</body>
</html>
