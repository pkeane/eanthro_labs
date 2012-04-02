<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<base href="{$module_root}"/>
		<title>DASe Installation & Configuration</title>
		<link rel="stylesheet" type="text/css" href="{$module_root}/css/style.css">
		<script type="text/javascript" src="{$app_root}www/scripts/Base64.js"></script> 
		<script type="text/javascript" src="{$app_root}www/scripts/json2.js"></script> 
		<script type="text/javascript" src="{$app_root}www/scripts/dase.js"></script> 
		<script type="text/javascript" src="{$app_root}www/scripts/dase/form.js"></script> 
		<script type="text/javascript" src="scripts/install.js"></script> 
	</head>
	<body>
		<div class="container">
			<div class="branding">
				DASe Installation & Configuration
			</div>
			<div class="content">
				<h3 class="msg">local_config.php is OK, proceed to:</h3>
				<form id="setupForm" action="db_setup" method="post">
					<table id="formTable">
						<tr>
							<th>
								<input type="submit" name="setup_tables" id="setup_tables_button" value="setup tables"/>
							</th>
							<td>
								<span id="setup_tables_msg" class="error"></span>
							</td>
						</tr>
						<tr id="create_admin" class="hide">
							<th>
								<input type="submit" name="create_admin" id="create_admin_button" value="create admin user"/>
							</th>
							<td>
								<span id="create_admin_msg" class="error"></span>
							</td>
						</tr>
						<tr id="create_sample" class="hide">
							<th>
							</th>
							<td>
								Please <a href="{$app_root}login/form">login</a>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>
