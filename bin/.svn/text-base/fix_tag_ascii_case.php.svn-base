<?php



include 'config.php';


$tag = new Dase_DBO_Tag($db);

foreach ($tag->findAll() as $t) {
	$lower = Dase_Util::dirify($t->ascii_id);
	if ($t->ascii_id != $lower) {
		print "PROBLEM! $t->ascii_id\n";
		$t->ascii_id = $lower;
		print $t;
		$t->update();

	} else {
		print "OK $t->ascii_id\n";
	}
}

