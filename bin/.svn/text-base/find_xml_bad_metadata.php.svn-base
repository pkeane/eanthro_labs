<?php

include 'config.php';

$ascii_id = 'arabic_proficiency';
$ascii_id = 'vrc';
$ascii_id = 'what_jane_saw';

$c = Dase_DBO_Collection::get($db,$ascii_id);

foreach($c->getItems() as $item) {
		foreach ($item->getValues() as $value) {
				$str = $value->value_text;
				try {
						$dom = new DOMDocument();
						if (!@$dom->loadXML('<s>'.htmlspecialchars($str).'</s>')) {
								print $str."\n";;
								$value->delete();
								$item->buildSearchIndex();
						}
				} 
				catch (Exception $e) {
						print "\n---------------------------\n".$str."\n";
						print $e->getMessage();
						exit;
				}
		}
}
