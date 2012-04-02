Dase.install = {};

Dase.install.resetErrors = function() {
	var spans = document.getElementsByTagName('span');
	for (var i=0;i<spans.length;i++) {
		if (spans[i].className == 'error') {
			spans[i].innerHTML = '';
		}
	}
	Dase.$('local_config_txt').className = 'hide';
};

Dase.pageInit = function() {
	var config_form = Dase.$('configForm');
	if (config_form) {
		Dase.install.config(config_form);
		return;
	}
	var setup_form = Dase.$('setupForm');
	if (setup_form) {
		Dase.install.setup(setup_form);
		return;
	}
}
	
Dase.install.config = function(form) {
	//deal with database type and sqlite db path input
	var type_select = form.db_type;
	if (type_select) {
		var db_path = Dase.$('db_path');
		if ('sqlite' != type_select.options[type_select.options.selectedIndex].value) {
			db_path.className = 'hide';
		}
		form.db_type.onchange = function() {
			if ('sqlite' != type_select.options[type_select.options.selectedIndex].value) {
				db_path.className = 'hide';
			} else {
				db_path.className = '';
			}
		}
	}
	Dase.$('config_check_button').onclick = function() {
		Dase.install.resetErrors();
		data = Dase.form.serialize(form);
		if ('' == form.eid.value) {
			Dase.$('eid_msg').innerHTML = 'username is required';
			return false;
		}
		if ('' == form.password.value) {
			Dase.$('password_msg').innerHTML = 'password is required';
			return false;
		}
		var content_headers = {
			'Content-Type':'application/x-www-form-urlencoded'
		}
		Dase.ajax(form.action,'post',function(resp) { 
				json = JSON.parse(resp);
				if (json.proceed) {
				//reload
				window.location.href = window.location.href;
				}
				if (!json.db) {
				Dase.$('db_msg').innerHTML = 'could not connect to db';
				}
				if (json.db && json.config) {
				Dase.$('config_check_msg').innerHTML = 'success!';
				Dase.$('local_config_msg').innerHTML = 'copy the following to '+json.local_config_path+':';
				Dase.$('local_config_txt').value = json.config;
				Dase.removeClass(Dase.$('local_config_txt'),'hide');
				}
				return;
		},data,null,null,content_headers); 
		return false;
	};
};

Dase.install.setup = function(form) {
	Dase.$('setup_tables_button').onclick = function() {
		Dase.ajax(Dase.base_href+'modules/install/setup_tables','post',function(resp) { 
				json = JSON.parse(resp);
				if (json.ok) {
				Dase.removeClass(Dase.$('create_admin'),'hide');
				}
				Dase.$('setup_tables_msg').innerHTML = json.msg;
				return;
		}); 
		return false;
	};
	Dase.$('create_admin_button').onclick = function() {
		Dase.ajax(Dase.base_href+'modules/install/create_admin','post',function(resp) { 
				json = JSON.parse(resp);
				if (json.ok) {
				Dase.removeClass(Dase.$('create_sample'),'hide');
				}
				Dase.$('create_admin_msg').innerHTML = json.msg;
				return;
		}); 
		return false;
	};
	Dase.$('create_sample_button').onclick = function() {
		Dase.ajax(Dase.base_href+'modules/install/setup_tables','post',function(resp) { 
				json = JSON.parse(resp);
				if (json.ok) {
				}
				Dase.$('create_sample_msg').innerHTML = json.msg;
				return;
		}); 
		return false;
	};
};
