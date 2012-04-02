<?php


include 'config.php';


$comms = new Dase_DBO_Comment($db);

foreach ($comms->find() as $c) {
	$item = new Dase_DBO_Item($db);
	$item->load($c->item_id);
	$item->comments_updated = $c->updated;
	$item->comments_count = $item->comment_count +1;
	$item;
	$item->update();
}

$cs = new Dase_DBO_Collection($db);

$i = 0;
foreach ($cs->find() as $c) {
	print "\nworking on $c->collection_name\n\n";

	foreach ($c->getItems() as $item) {
		$i++;
		$item = clone($item);
		if (!$item->collection_name) {
			$item->collection_name = $c->collection_name;
			$type = $item->getItemType();
			$item->item_type_ascii_id = $type->ascii_id;
			$item->item_type_name = $type->name;
			$item->update();
		}
		print "\n$i";
	}
}


