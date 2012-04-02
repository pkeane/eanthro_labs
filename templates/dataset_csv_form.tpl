<div class="generic_content">
	<h1>Upload CSV</h1>
	<p>CSV file should contain rows, the first five column of
	which should represent: gender, age, height, foot length,
	stride length.</p>
	<form action="dataset/{$dataset->id}/csv" method="post" enctype="multipart/form-data">
		<label for="uploaded_file">select a file</label>
		<input type="file" name="uploaded_file"/>
		<input type="submit" value="upload csv file"/>
	</form>
</div>
