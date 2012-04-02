<?php

include 'config.php';

$feed = Dase_Atom_Feed::load('gfromr.atom');

$records = array();
$attributes = array();
$att_count = array();
foreach ($feed->entries as $entry) {
	$record = array();
	$attribute = array();
	$etype = $entry->getEntryType();
	$search_set = '';
	$search = array();
	if ('item' == $etype) {
	$record['record_title'] = $entry->getTitle();
	$record['serial_number'] = $entry->getSerialNumber();
	$record['enclosure'] = $entry->getEnclosure();
	foreach ($entry->getMetadata() as $ascii => $meta) {
		if (!isset($att_count[$ascii])) {
			$att_count[$ascii] = 1;
		} else {
			$att_count[$ascii]++;
		}
		$record[$ascii] = $meta;
		foreach ($meta['values'] as $v) {
			$search_set .= " $v";
		}
	}
	$search[$record['serial_number']] = $search_set;
	$records['searches'][] = $search;
	$records['items'][$record['serial_number']] = $record;
	} else {
		$attribute['title'] = $entry->getTitle();
		$attribute['ascii_id'] = $entry->getAsciiId();
		$attributes[] = $attribute;
	}
}
foreach ($attributes as $a) {
	if (isset($att_count[$a['ascii_id']])) {
		$a['count'] = $att_count[$a['ascii_id']];
		$records['attributes'][] = $a;
	}
}
print Dase_Json::get($records);

