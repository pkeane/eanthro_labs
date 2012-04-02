{extends file="trackways.tpl"}

{block name="head-scripts"}
<script src="www/js/jquery.colorbox.js"></script>
<script src="www/js/photos.js"></script>
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
<div class="generic_content">
<p>{$content.body}</p>
<div id="photo_set">
{foreach from=$photos item=photo}
<a href="{$photo->file_url}"><img src="{$photo->thumbnail_url}"></a>
{/foreach}
</div>

{if $request->user->eid}
<a id="contribute" href="photos/contribute">upload/manage your Trackways photos</a>
{else}
<a href="login?target=trackways/share_photos">login</a> to contribute your photos
{/if}

</div>
{/block}
