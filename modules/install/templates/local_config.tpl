<?php

$conf['db']['type'] = '{$db.type}';
$conf['db']['host'] = '{$db.host}';
$conf['db']['name'] = '{$db.name}';
$conf['db']['user'] = '{$db.user}';
$conf['db']['pass'] = '{$db.pass}';
$conf['db']['table_prefix'] = '{$table_prefix}';

$conf['app']['main_title'] = '{$main_title}';
$conf['app']['path_to_local_css'] = '';
$conf['app']['page_logo']['link_target'] = '';
$conf['app']['page_logo']['src'] = '';
$conf['app']['page_logo']['alt'] = '';
$conf['app']['convert'] = '{$convert_path}';
$conf['app']['default_handler'] = 'collections';

$conf['auth']['superuser']['{$eid}'] = '{$password}';
$conf['auth']['token'] = '{$token}';
$conf['auth']['ppd_token'] = "{$ppd_token}";
$conf['auth']['serviceuser']['test'] = '1';
$conf['auth']['service_token'] = "{$service_token}";

//$conf['request_handler']['login'] = 'openid';
//$conf['request_handler']['db'] = 'dbadmin';

