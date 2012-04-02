<?php

include 'config.php';

$c= Dase_DBO_Collection::get($db,'cola_images');
$c= Dase_DBO_Collection::get($db,'pkeane');

foreach ($c->getItems() as $item) {
		$item = clone($item);
		foreach ($item->getValues() as $v) {
				$v = clone($v);
				if (strpos($v->value_text," \xA9")) {
						print $v->value_text."\n";
						$v->value_text = str_replace( " \xA9", ' copyright', $v->value_text); 
						$v->update();
						//$item->buildSearchIndex();
				}
		}
}

$c->buildSearchIndex('');
