<?php

$conf['db']['type'] = 'mysql';
$conf['db']['host'] = 'mysql.laits.utexas.edu';
$conf['db']['name'] = 'pkeane_eanthro_cms';
$conf['db']['user'] = 'eanthro_cms';
$conf['db']['pass'] = 'ArtgfK7dC0csM9mI3q';
$conf['db']['table_prefix'] = '';

$conf['app']['main_title'] = 'eAnthroLabs CMS';
$conf['app']['default_handler'] = 'home';
//$conf['request_handler']['login'] = 'uteid';
$conf['request_handler']['login'] = 'google';
$conf['app']['user_class'] = 'Dase_DBO_User';
$conf['app']['log_level'] = 3;
$conf['app']['init_global_data'] = false;

$conf['app']['media_dir'] = '/mnt/www-data/eanthro/cms'; 

$conf['auth']['superuser']['pkeane'] = 'eanthro';
$conf['auth']['token'] = 'auth';
$conf['auth']['ppd_token'] = "ppd";
$conf['auth']['service_token'] = "service";
$conf['auth']['serviceuser']['test'] = 'ok';

