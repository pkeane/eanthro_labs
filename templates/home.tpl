{extends file="labs_layout.tpl"}

{block name="content"}
<div id="home-content">
	<div id="banners">
		<img id="front" src="file/eanthrolabs_eanthrobanner.jpg">
		<img class="hide" id="tw" src="file/eanthrolabs_trackwaysbanner.jpg">
		<img class="hide" id="tm" src="file/eanthrolabs_tempmigratebanner.jpg">
		<img class="hide" id="id" src="file/e_anthrolabs_ideabanners.jpg">
	</div>

	<ul id="lab_selector">
		{foreach item=thumb from=$lab_thumbs.items}
		<li class="{$thumb.meta2}" >
		<h4>{$thumb.title}</h4>
		<a href="{$thumb.meta1}"><img src="{$thumb.links.file}"></a>
		</li>
		{/foreach}
	</ul>
</div>
{/block}
