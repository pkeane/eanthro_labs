{extends file="labs_layout.tpl"}

{block name="head-links"}
<link rel="stylesheet" href="www/css/colorbox.css">
{/block}

{block name="head-scripts"}
<script src="www/js/jquery.colorbox.js"></script>
{/block}

{block name="content"}
<ul id="mocks">
	{foreach item=mock from=$mocks.items}
	<li>
	<h4>{$mock.title}</h4>
	<!--
	<a href="item/{$mock.item_id}/box"><img src="{$mock.links.thumbnail}"></a>
	-->
	<a href="{$mock.links.file}"><img src="{$mock.links.thumbnail}"></a>
	</li>
	{/foreach}
</ul>
{/block}
