<div class="generic_content">
	<h1>Share a Photo</h1>
	<form action="photos/uploader" method="post" enctype="multipart/form-data">
		<!--
		<label for="title">title</label>
		<input type="text" name="title"/>
		<label for="body">body</label>
		<textarea name="body"></textarea>
		-->
		<label for="uploaded_file">select a file</label>
		<input type="file" name="uploaded_file"/>
		<p>
		<input type="submit" value="upload file"/>
		</p>
	</form>
	<ul id="my_photo_set">
		{foreach from=$photos item=photo}
		<li>
	 	<a href="photos/{$photo->id}" class="delete">[delete]</a>
		<img src="{$photo->thumbnail_url}">
		<li>
		{/foreach}
	</ul>

</div>
