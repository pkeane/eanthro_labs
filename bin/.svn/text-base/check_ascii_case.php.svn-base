<?php



include 'config.php';


$colls = array();

$att = new Dase_DBO_Attribute($db);

foreach ($att->findAll() as $a) {
	if ($a->ascii_id != Dase_Util::dirify($a->ascii_id)) {
		print "PROBLEM! $a->ascii_id  ";
		$coll = $a->getCollection();
		print  "collection $coll->ascii_id ";
		$colls[$coll->ascii_id] = $coll->collection_name;
		print  " values count: ".$a->getValuesCount();
		print  " item types count: ".count($a->getItemTypes());
		print  " defined values count: ".count($a->getDefinedValues());
		$lower = Dase_Util::dirify($a->ascii_id);
		$a->ascii_id = $lower;

		//$a->update();
		print $a;
		print "\n";
	} else {
		print "OK $a->ascii_id\n";
	}
}

print_r($colls);
