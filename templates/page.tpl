{extends file="labs_layout.tpl"}

{block name="content"}
<h1>{$item->title}</h1>
<p>
{$body->body|markdown}
</p>
<img src="{$item->thumbnail_url}">
{/block}
