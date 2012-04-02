<?php


//rework this to use dase client

include 'config.php';

$eid = 'pkeane';

$sets_url = 'http://quickdraw.laits.utexas.edu/dase1/user/'.$eid.'/sets.atom?auth=http';

$user = 'pkeane';
print "enter password for user $user:\n";
system('stty -echo');
$pass = trim(fgets(STDIN));
system('stty echo');

$auth = base64_encode($user.':'.$pass);
$header = array("Authorization: Basic $auth");
$opts = array( 'http' => array ('method'=>'GET','header'=>$header));
$ctx = stream_context_create($opts);

print file_get_contents($sets_url,false,$ctx);

