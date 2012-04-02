<?php

include 'config.php';

$solr_url = 'quickdraw.laits.utexas.edu:8080/solr/update';
$limit = 0;
//$limit = 15;

if ($limit) {
	print "\nCurrently set to index most recent $limit items\n";
}

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

$engine = new Dase_Solr($db,$config);

$i = 0;

foreach ($colls as $coll) {

	$c = Dase_DBO_Collection::get($db,$coll);

	if ($c) {
		foreach ($c->getItems($limit) as $item) {
			$i++;
			$item = clone($item);
			print $c->collection_name.':'.$item->serial_number.':'.$item->buildSearchIndex(false,false);
			print " $i\n";
			print " memory: ".memory_get_usage()."\n";
		}
		print "\ncommitting indexes for $c->collection_name ";
		$engine->commit();
		print "...done\n\n";
	}
}

