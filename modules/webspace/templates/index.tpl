{extends file="../../../templates/manage/layout.tpl"}

{block name="head"}
<link rel="stylesheet" type="text/css" href="{$module_root}css/style.css">
<script type="text/javascript" src="{$module_root}scripts/webspace.js"></script>
{/block}

{block name="content"}
<h1>UTexas WebSpace Ingester</h1>
<div class="content">
	<form action="manage/{$collection->ascii_id}/webspace" method="get">
		<span
			class="wspace">http://webspace.utexas.edu/</span><input
		type="text" size="40" name="webspace_path" value="{$webspace_path}"/>
		<input type="submit" value="retrieve file listing"/>
	</form>
	{if $files|@count}
	<div class="file_list">
		<h3>media files</h3>
		<form id="ingester" action="ingester">
			<ul class="multicheck" id="fileList">
				{foreach item=file from=$files}
				<li>
				<img src="{$app_root}www/images/indicator.gif" class="hide"/>
				<input type="checkbox" checked="checked" value="{$file.url}" name="file_to_upload"/>
				<a class="checked" href="{$file.url}">{$file.name}</a>
				<span class="filesize">({$file.length}K)</span>
				</li>
				{/foreach}
			</ul>
			<p id="checker">
			<a href="#" id="checkall">check/uncheck all</a>
			</p>
			<input id="submitButton" type="submit" value="upload checked files"/>
		</form>
	</div>
	{/if}
	{if $paths|@count}
	<div class="directory_list">
		<h3>directories</h3>
		<ul>
			{foreach item=path from=$paths}
			<li>
			<a href="manage/{$collection->ascii_id}/webspace?webspace_path={$path.path_rel}">/{$path.path_name}</a>
			</li>
			{/foreach}
		</ul>
	</div>
	{/if}
	{if $invalid_files|@count}
	<div class="invalid_list">
		<h3>unsupported files</h3>
		<ul>
			{foreach item=ifile from=$invalid_files}
			<li>{$ifile.name} ({$ifile.type})</li>
			{/foreach}
		</ul>
	</div>
	{/if}
</div>
{/block}
