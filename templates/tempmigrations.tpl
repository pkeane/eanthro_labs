{extends file="labs_layout.tpl"}

{block name="head-scripts"}
{/block}

{block name="content"}
<img src="{$page_content.links.file}">
<div class="generic_content">
<p>{$page_content.body|markdown}</p>
</div>
{/block}
