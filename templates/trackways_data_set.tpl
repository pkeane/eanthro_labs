{extends file="trackways.tpl"}

{block name="head-scripts"}
<script src="www/js/instructions.js"></script>
<script src="www/js/jquery.colorbox.js"></script>
<script>
	{literal}
	$(document).ready(function(){
		$('a#csv').click(function() {
			var href = $(this).attr('href');
			$.colorbox({
				href:href,
				width: '480',
				opacity: 0.5,
				onComplete: function() {
					$('#closeColorbox').click(function() {$.colorbox.close();});
				}
			}); 
			return false;
		});
	});
	{/literal}
</script>
{/block}

{block name="head-links"}
<link rel="stylesheet" type="text/css" href="www/css/colorbox.css" /> 
{/block}

{block name="inner-header"}
<div class="content_header">
	<h1>Trackways Exercise <em>On the Track of Prehistoric Humans</em></h1>
</div>
{/block}

{block name="trackways_content"}
<div class="section">
	<div class="controls">
		{if $data_set->is_published}
		<form action="dataset/{$data_set->id}/published" method="post">
			<input type="hidden" value="0" name="state">
			<span class="pub_state_on">published</span> |
			<input type="submit" value="unpublish data set">	
		</form>
		{else}
		<form action="dataset/{$data_set->id}/published" method="post">
			<input type="hidden" value="1" name="state">
			<span class="pub_state_off">unpublished</span> |
			<input type="submit" value="publish data set">	
		</form>
		{/if}
	</div>
	<h1>Data set: {$data_set->name}</h1>
	<!--
	<p>
	{$node->body|markdown}
	</p>
	-->
	<h2>Enter Data</h2>
	<form id="data_form" action="dataset/{$data_set->id}" method="post">
		<table class="data_form">
			<tr>
				<th>Gender</th>
				<th class="age">Age</th>
				<th>Height (cm)</th>
				<th>Foot Length (cm)</th>
				<th>Stride Length (cm)</th>
				<th></th>
			</tr>
			<tr>
				<th class="gender">
					<input type="radio" name="gender" value="female"><span class="radiolabel">F</span> 
					<input type="radio" name="gender" value="male"><span class="radiolabel">M </span>
				</th>
				<th class="age">
					<input type="text" name="age"> 
				</th>
				<th>
					<input type="text" name="height"> 
				</th>
				<th>
					<input type="text" name="foot_length"> 
				</th>
				<th>
					<input type="text" name="stride_length"> 
				</th>
				<th>
					<input type="submit" value="submit!"> 
				</th>
			</tr>
			{foreach item=pd from=$data_set->person_datas name="ss"}
			<tr {if 0 == $smarty.foreach.ss.iteration%2}class="even"{/if}>
				<td>{$pd->gender}</td>
				<td>{$pd->age}</td>
				<td>{$pd->height} cm</td>
				<td>{$pd->foot_length} cm</td>
				<td>{$pd->stride_length} cm</td>
				<td><a class="delete" href="dataset/{$data_set->id}/person_data/{$pd->id}">[delete]</a></td>
			</tr>
			{/foreach}
		</table>
	</form>
	<a id="csv" href="dataset/{$data_set->id}/csv">upload a CSV data sheet</a>
	{if 0 == $data_set->is_published}
	<div class="controls">
		<form method="delete" action="dataset/{$data_set->id}">
			<input type="submit" value="delete data set (ALL data will be deleted)">
		</form>
	</div>
	{/if}
	<div class="clear"></div>
	<!--
	<div id="data_table"></div>
	-->
</div>
{/block}
