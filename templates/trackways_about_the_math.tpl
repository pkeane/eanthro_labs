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
	{$math.body|markdown}
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
			{foreach item=concept from=$concepts.items}
			<div class="step{$concept.meta2} {if $concept.meta2 == 1}selected{/if}">
				<h1>{$concept.title}: </h1>
				<p>{$concept.body|markdown}</p>
				<img src="{$concept.links.file}"/>
			</div>
			{/foreach}
		</div> <!-- close step_viewer -->
	</div> <!-- close instructions -->
</div>
{/block}
