{extends file="trackways.tpl"}

{block name="head-links"}
		<link rel="stylesheet" type="text/css" href="www/css/colorbox.css" /> 
{/block}

{block name="head-scripts"}
<script src="www/js/instructions.js"></script>
<script src="www/js/jquery.colorbox.js"></script>
<script>
	{literal}
	$(document).ready(function(){
		$(".youtube").colorbox({iframe:true, innerWidth:560, innerHeight:315});
	});
	{/literal}
</script>
{/block}

{block name="inner-header"}
<div class="content_header">
	<h1>Trackways Exercise <em>On the Track of Prehistoric Humans</em></h1>
</div>
{/block}

{block name="trackways_content"}
<div class="generic_content">
	<p>
	{$instr.body}
	<a class='youtube' href="http://www.youtube.com/embed/4ZbTo8TtRQw?rel=0&amp;wmode=transparent" title="The Trackways Lab: On The Track of Prehistoric Humans ">Check out the Trackways Video!</a>
	</p>
	<div class="instructions_container clearfix">
		<div class="step_thumbnails">
			<ul>
				{foreach item=item from=$thumbs.items}
				<li class="step{$item.meta2} {if $item.meta2 == 1}selected{/if}"><img src="{$item.links.file}"/></li>
				{/foreach}
			</ul>
		</div>
		<div class="step_viewer">
			{foreach item=step from=$steps.items}
			<div class="step{$step.meta2} {if $step.meta2 == 1}selected{/if}">
				<h1>Step {$step.meta2}: </h1>
				<p>{$step.body}</p>
				<img src="{$step.links.file}"/>
			</div>
			{/foreach}
		</div> <!-- close step_viewer -->
	</div> <!-- close instructions -->
</div>
{/block}
