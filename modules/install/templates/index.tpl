<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<base href="{$module_root}"/>
		<title>DASe Installation & Configuration</title>
		<link rel="stylesheet" type="text/css" href="{$module_root}css/style.css">
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
				<form id="configForm" action="config_checker" method="post">
					<table id="formTable">
						<tr>
							<th>
								<label for="main_title">DASe Archive Title</label>
							</th>
							<td>
								<input type="text" name="main_title" value="{$conf.main_title}"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="eid">Admin Username</label>
							</th>
							<td>
								<input type="text" name="eid" value="{$eid}"/>
								<span id="eid_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="password">Admin Password</label>
							</th>
							<td>
								<input type="text" name="password" value="{$password}"/>
								<span id="password_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="convert_path">Path to ImageMagick "convert" Utility</label>
							</th>
							<td>
								<input type="text" name="convert_path" value="{$convert_path}"/>
								<span id="convert_path_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="table_prefix">Table Prefix (optional)</label>
							</th>
							<td>
								<input type="text" name="table_prefix" value="{$conf.table_prefix}"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="db_type">Database Type</label>
							</th>
							<td>
								<select name="db_type">
									<option {if 'mysql' == $conf.db.type}selected="selected"{/if} value="mysql">MySQL</option>
									<option {if 'pgsql' == $conf.db.type}selected="selected"{/if} value="pgsql">PostgreSQL</option>
									<option {if 'sqlite' == $conf.db.type}selected="selected"{/if} value="sqlite">SQLite</option>
								</select>
								<span id="db_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="db_host">Database Host</label>
							</th>
							<td>
								<input type="text" name="db_host" value="{$conf.db.host}"/>
								<span id="db_host_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="db_name">Database Name</label>
							</th>
							<td>
								<input type="text" name="db_name" value="{$conf.db.name}"/>
								<span id="db_name_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="db_user">Database User</label>
							</th>
							<td>
								<input type="text" name="db_user" value="{$conf.db.user}"/>
								<span id="db_user_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th>
								<label for="db_pass">Database Password</label>
							</th>
							<td>
								<input type="text" name="db_pass" value="{$conf.db.pass}"/>
								<span id="db_pass_msg" class="error"></span>
							</td>
						</tr>
						<tr id="db_path">
							<th>
								<label for="db_path">Database Path (SQLite only)</label>
							</th>
							<td>
								<input type="text" size="40" name="db_path" value="{$conf.db.path}"/>
								<span id="db_path_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th></th>
							<td>
								<input type="submit" name="config_check" id="config_check_button" value="check configuration settings"/>
								<span id="config_check_msg" class="error"></span>
							</td>
						</tr>
						<tr>
							<th></th>
							<td>
								<span id="local_config_msg" class="error"></span>
								<textarea id="local_config_txt" class="hide" cols="80" rows="28" name="local_config"></textarea>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>
