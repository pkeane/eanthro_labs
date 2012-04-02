<?php

include 'config.php';

//this script replaces characters which are valid utf8
//but invalid in XML (there are just a few)


$tis = new Dase_DBO_TagItem($db);

foreach($tis->find() as $ti) {
	if ($ti->annotation != strip_invalid_xml_chars2($ti->annotation)) {
		$tag = $ti->getTag();
		print $tag->ascii_id."\n";
		//$ti->annotation = strip_invalid_xml_chars2($ti->annotation);
		//$ti->update();
		file_put_contents('bad',$ti->annotation);
		print "found bad xml in tag_item $ti->id\n";
		print $tag->dase_user_id."\n";
		//print $ti->annotation."\n";
	}
}


function strip_invalid_xml_chars2( $in ) {
	$out = "";
	$length = strlen($in);
	for ( $i = 0; $i < $length; $i++) {
		$current = ord($in{$i});
		if (($current == 0x9) ||
			($current == 0xA) || 
			($current == 0xD) || 
			($current >= 0x20 && $current <= 0xD7FF) || 
			($current >= 0xE000 && $current <= 0xFFFD) || 
			($current >= 0x10000 && $current <= 0x10FFFF)
		){
			$out .= chr($current);
		} else{
			print $current."\n";
			$out .= " ";
		}
	}
	return $out;
}
