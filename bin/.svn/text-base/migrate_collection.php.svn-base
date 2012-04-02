<?php
include 'config.php';

$source = 'mexican_american_experience';
$target = 'ondas_indigenas';

$c = Dase_DBO_Collection::get($db,$source);

foreach ($c->getItems() as $item) {
	$item = clone($item);
	$json_doc = $item->buildJson('http://dase.laits.utexas.edu');
	//print_r(json_decode($json_doc));
	$target_url = 'https://daseupload.laits.utexas.edu/collection/'.$target.'/ingester';
	print $target_url."\n";
	$res = Dase_Http::post($target_url,$json_doc,'pkeane','dupload','application/json');
	print_r($res);
}


