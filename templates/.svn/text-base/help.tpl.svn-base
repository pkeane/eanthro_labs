{extends file="layout.tpl"}
{block name="title"}DASe Help{/block} 

{block name="content"}
<div class="full" id="browse">
	{if $msg}<h3 class="alert">{$msg}</h3>{/if}
	<h1>DASe Frequently Asked Questions</h1>

	<dl id="faq">
		{foreach item=q from=$collection->entries}
		<dt>{$q->question.text}</dt>
		<dd>{$q->answer.text}</dd>
		{/foreach}
	</dl>
</div>
{/block}
