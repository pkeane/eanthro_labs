{extends file="labs_layout.tpl"}

{block name="head-scripts"}
<script src="www/js/cross.js"></script>
<script src="www/js/front.js"></script>
{/block}

{block name="content"}
<div id="trackways-menu">
	<ul id="tmenu">
		<li {if $page == 'lab_home'}class="active"{/if}>
		<a href="trackways">Lab Home</a>
		</li>
		<li {if $page == 'instructions'}class="active"{/if}>
		<a href="trackways/instructions">Instructions</a>
		</li>
		<li {if $page == 'data_sets'}class="active"{/if}>
		<a href="trackways/data_sets">My Data Sets</a>
		</li>
		<li {if $page == 'graph_data'}class="active"{/if}>
		<a href="trackways/graph_data">Graph Data</a>
		</li>
		<li {if $page == 'about_the_math'}class="active"{/if}>
		<a href="trackways/about_the_math">About the Math</a>
		</li>
		<li {if $page == 'share_photos'}class="active"{/if}>
		<a href="trackways/share_photos">Share Photos</a>
		</li>
		<li>
		<a href="#" class="toggle" id="toggleDatasheetLinks">Download Datasheet</a>
		<ul id="targetDatasheetLinks" class="hide">
			<li><a href="http://dev.laits.utexas.edu/eanthro/labs/file/trackwaysenglish_datasheet.pdf">English</a></li>
			<li><a href="http://dev.laits.utexas.edu/eanthro/labs/file/trackwayscroatian_datasheet.pdf">Croatian</a></li>
			<li><a href="http://dev.laits.utexas.edu/eanthro/labs/file/trackwaysnorwegian_datasheet.pdf">Norwegian</a></li>
		</ul>
		</li>
	</ul>
	<div class="clear"></div>
</div>
{block name="inner-header"}
<img class="default_banner" src="file/trackways_modern_banner.jpg" style="background: url(file/trackways_old_banner.jpg);"/>
{/block}

<div class="text-content">
	{block name="trackways_content"}
	<div class="front-content">
		{$front.body|markdown}
	</div>
	{/block}
</div>
<div class="clear"></div>
{/block}
