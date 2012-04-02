<?php

include 'config.php';

//this script rebuilds search indexes

$coll_ascii_id = 'what_jane_saw';
$coll_ascii_id = 'cola_images';

$coll = new Dase_DBO_Collection($db);
$coll->orderBy('item_count ASC');
if ($coll_ascii_id) {
	$coll->ascii_id = $coll_ascii_id;
}
foreach ($coll->find() as $c) {
	$start = Dase_Util::getTime();
	print "working on " . $c->collection_name . "(".$c->item_count." items)\n";
	$c->buildSearchIndex('');
	$end = Dase_Util::getTime();
	$total = $end - $start;
	print ($total . " seconds\n");
}
