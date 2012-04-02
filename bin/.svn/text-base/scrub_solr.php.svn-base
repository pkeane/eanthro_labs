<?php

include 'config.php';
$c = 'ondas_indigenas';
$c = 'arabic_proficiency';
$c = 'philosophy_reading_room';
$res = scrubIndex($c,$config,$db);

function scrubIndex($collection_ascii_id,$config,$db,$display=true)
{
	$solr_version = '2.2';
	$solr_base_url = $config->getSearch('solr_base_url');
	$solr_update_url = $solr_base_url.'/update';
	$j = 0;
	for ($i=0;$i<999999;$i++) {
		if (0 === $i%100) {
			$solr_search_url = 
				$solr_base_url
				.'/select/?q=c:'
				.$collection_ascii_id
				.'&version='
				.$solr_version
				.'&rows=100&start='.$i;
			list($http_code,$res) = Dase_Http::get($solr_search_url,null,null);
			$sx = simplexml_load_string($res);
			$num = 0;
			foreach ($sx->result as $result) {
				if (count($result->doc)) {
					foreach ($result->doc as $doc) {
						foreach ($doc->str as $str) {
							if ('_id' == $str['name']) {
								$j++;
								if ($display) {
									print "START $i ($j) ";
								}
								$unique = (String) $str;
								if (Dase_DBO_Item::getByUnique($db,$unique)) {
									if ($display) {
										print "FOUND $unique\n";
									}
								} else {
									$num++;
									$delete_doc = '<delete><id>'.$unique.'</id></delete>';
									$resp = Dase_Http::post($solr_update_url,$delete_doc,null,null,'text/xml');
									if ($display) {
										print "SCRUBBED $unique\n";
									}
								}
							}
						}
					}
				} else {
					Dase_Http::post($solr_update_url,'<commit/>',null,null,'text/xml');
					return "scrubbed $num records";
				}
			}
		}
	}
}

