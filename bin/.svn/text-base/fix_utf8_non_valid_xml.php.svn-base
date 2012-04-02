<?php

include 'config.php';

//this script replaces characters which are valid utf8
//but invalid in XML (there are just a few)

$ascii_id = 'cumbojd';

$c = Dase_DBO_Collection::get($db,$ascii_id);

foreach($c->getItems() as $item) {
		$item = clone($item);
		print "working\n";
			$item->deleteDocs();
			$item->storeDoc();
	foreach ($item->getValues() as $value) {
		$str = $value->value_text;
//		print $str."\n";
		if ($str != strip_invalid_xml_chars2($str)) {
			$value->value_text = strip_invalid_xml_chars2($str);
			$value->update();
			print "updated item $item->serial_number\n";
		}
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
			$out .= " ";
		}
	}
	return $out;
}
