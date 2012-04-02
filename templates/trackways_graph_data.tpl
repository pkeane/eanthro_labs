{extends file="trackways.tpl"}

{block name="head-scripts"}
<script src="www/js/highcharts.js"></script>
<script src="www/js/foot_length_graph.js"></script>
<script src="www/js/stride_length_graph.js"></script>
{/block}

{block name="head-links"}
<link rel="foot_length_data" type="application/json" href="datasets/foot_length/{$eid_filter}.json?cb={$time}">
<link rel="stride_length_data" type="application/json" href="datasets/stride_length/{$eid_filter}.json?cb={$time}">
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
	{if $request->user->eid}
	<div class="controls">
		<div class="view_published_links">
			<a href="trackways/graph_data">view ALL published data sets</a> |
			<a href="trackways/graph_data/{$request->user->eid}">view {$request->user->eid}'s  published data sets</a>
		</div>
	</div>
	<div class="clear"></div>
	{/if}
	<div id="foot_length_graph"></div>
	<div class="clear"></div>
	<div id="stride_length_graph"></div>
</div>
{/block}
