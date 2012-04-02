<?php

include 'config.php';

$cs = new Dase_DBO_Collection($db);
$cs->orderBy('id DESC');
foreach ($cs->find() as $c) {
	$c = clone($c);
	$colls[] = $c->ascii_id;
}

//can enter collections on command line
if (isset($argv[1])) {
	array_shift($argv);
	$colls = $argv;
}

$i = 0;
foreach ($colls as $coll) {

	$c = Dase_DBO_Collection::get($db,$coll);

	if ($c) {
		foreach ($c->getItems() as $item) {
			$i++;
			$item = clone($item);
			print $c->collection_name.':'.$item->serial_number.':'.$item->storeDoc();
			print " $i\n";
			print " memory: ".memory_get_usage()."\n";
		}
	}
}

