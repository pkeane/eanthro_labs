<?php

include 'config.php';

$pds = new Dase_DBO_PersonData($db);

$x_data = array();
$y_data = array();
foreach ($pds->findAll(1) as $pd) {
		$x_data[] = $pd->foot_length;
		$y_data[] = $pd->height;
}

print json_encode(Dase_Util::linReg($x_data,$y_data));
