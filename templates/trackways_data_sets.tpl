{extends file="trackways.tpl"}

{block name="head-scripts"}
<script src="www/js/instructions.js"></script>
{/block}

{block name="inner-header"}
<div class="content_header">
	<h1>Trackways Exercise <em>On the Track of Prehistoric Humans</em></h1>
</div>
{/block}

{block name="trackways_content"}
<div class="generic_content">
	<p>
	{$page_content.body}
	</p>
	<form id="data_set_create" method="post" action="datasets/{$request->user->eid}">
		<input type="text" name="name">
		<input type="submit" value="add data set">
	</form>
	<h2>{$request->user->eid}'s data sets:</h2>
	<ul class="datasets">
		{foreach item=set from=$data_sets}
		<li><a href="trackways/data_set/{$set->id}">{$set->name}</a></li>
		{/foreach}
	</ul>
	<div class="clear"></div>
</div>
{/block}
