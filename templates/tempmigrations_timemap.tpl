{extends file="labs_layout.tpl"}

{block name="head-links"}
<link rel="stylesheet" href="www/css/timemap.css" />
{/block}

{block name="head-scripts"}
<script src="http://maps.google.com/maps?file=api&v=2&sensor=false&key=AIzaSyDLxM53miuaJXWi7QDrmz4euof5uexG13Y"></script>
<script src="http://api.simile-widgets.org/timeline/2.3.1/timeline-api.js"></script>
<script src="http://api.simile-widgets.org/timeline/2.3.1/ext/geochrono/geochrono-api.js"></script>
<script src="www/js/timemap/timemap.js"></script>
<script src="www/data/data.js"></script>
<script src="www/js/tempmig.js"></script>
{/block}

{block name="content"}
<div id="timemap">
	<div id="timelinecontainer"><div id="timeline"></div></div>
	<div id="mapcontainer"><div id="map"></div></div>i
</div>
{/block}
