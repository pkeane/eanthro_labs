<?php

class Dase_Handler_Collection extends Dase_Handler
{
	public $collection;
	public $resource_map = array(
		'{collection_ascii_id}' => 'collection',
		'{collection_ascii_id}/commit' => 'commit',
		//for json dump of all data
		'{collection_ascii_id}/dump' => 'dump',
		'{collection_ascii_id}/entry' => 'entry',
		'{collection_ascii_id}/archive' => 'archive',
		'{collection_ascii_id}/last_serial_number' => 'last_serial_number',
		'{collection_ascii_id}/ping' => 'ping',
		'{collection_ascii_id}/profile' => 'profile',
		'{collection_ascii_id}/search' => 'search',
		'{collection_ascii_id}/media' => 'media_urls',
		'{collection_ascii_id}/search/item' => 'search_item',
		'{collection_ascii_id}/ingester' => 'ingester',
		'{collection_ascii_id}/serial_numbers' => 'serial_numbers',
		'{collection_ascii_id}/admin_attributes' => 'admin_attributes',
		'{collection_ascii_id}/admin_attribute_tallies' => 'admin_attribute_tallies',
		'{collection_ascii_id}/attributes' => 'attributes',
		//todo: implement
		'{collection_ascii_id}/attribute/{att_ascii_id}' => 'attribute',
		'{collection_ascii_id}/attribute_tallies' => 'attribute_tallies',
		'{collection_ascii_id}/service' => 'service',
		'{collection_ascii_id}/items' => 'items',
		'{collection_ascii_id}/items/{start}:{end}' => 'items_by_range',
		'{collection_ascii_id}/item_types' => 'item_types',
		//todo implement:
		'{collection_ascii_id}/items/recent' => 'recent_items',
		'{collection_ascii_id}/items/by/md5/{md5}' => 'items_by_md5',
		'{collection_ascii_id}/items/by/att/{att_ascii_id}' => 'items_by_att',
		'{collection_ascii_id}/items/that/lack_media' => 'items_that_lack_media',
		'{collection_ascii_id}/deletions' => 'items_marked_to_be_deleted',
		'{collection_ascii_id}/managers' => 'managers',
	);

	protected function setup($r)
	{
		$this->collection = Dase_DBO_Collection::get($this->db,$r->get('collection_ascii_id'));
		if (!$this->collection) {
			$r->renderError(404);
		}
		if ('html' == $r->format && 
			'service' != $r->resource &&
			'ping' != $r->resource
		) {
			$this->user = $r->getUser();
			if (!$this->user->can('read',$this->collection)) {
				$r->renderError(401);
			}
		}
		/* todo: i guess anyone can read?
		if ('atom' == $r->format) {
			$this->user = $r->getUser('http');
			if (!$this->user->can('read',$this->collection)) {
			$r->renderError(401);
			}
		}
		 */
	}

	public function getCollectionJson($r)
	{
		$r->renderResponse($this->collection->asJson($r->app_root));
	}

	public function getSearch($r) 
	{
		$search_handler = new Dase_Handler_Search($this->db,$this->config); 
		$search_handler->setup($r);
		$r->set('collection_ascii_id',$this->collection->ascii_id);
		$search_handler->getSearch($r);
	}

	public function getSearchAtom($r) 
	{
		$search_handler = new Dase_Handler_Search($this->db,$this->config); 
		$search_handler->setup($r);
		$r->set('collection_ascii_id',$this->collection->ascii_id);
		$search_handler->getSearchAtom($r);
	}

	public function getSearchItem($r) 
	{
		$search_handler = new Dase_Handler_Search($this->db,$this->config); 
		$search_handler->setup($r);
		$r->set('collection_ascii_id',$this->collection->ascii_id);
		$search_handler->getSearchItem($r);
	}

	public function getSearchItemAtom($r) 
	{
		$search_handler = new Dase_Handler_Search($this->db,$this->config); 
		$search_handler->setup($r);
		$r->set('collection_ascii_id',$this->collection->ascii_id);
		$search_handler->getSearchItemAtom($r);
	}

	public function getSearchJson($r) 
	{
		$search_handler = new Dase_Handler_Search($this->db,$this->config); 
		$search_handler->setup($r);
		$r->set('collection_ascii_id',$this->collection->ascii_id);
		$search_handler->getSearchJson($r);
	}


	public function getManagersJson($r)
	{
		foreach ($this->collection->getManagers() as $obj) {
			$managers[$obj->dase_user_eid] = $obj->auth_level;
		}
		$r->renderResponse(Dase_Json::get($managers));
	}

	public function getMediaUrls($r)
	{
		$limit = $r->get('limit');
		if (!$limit) {
			$limit = 200;
		}
		$set = array();
		$tpl = new Dase_Template($r);
		foreach ($this->collection->getItems($limit) as $item) {
			$item = clone($item);
			$enclosure_url = $item->getEnclosure();
			$set[$r->app_root.$enclosure_url['url']] = $item->getTitle();
		}	
		asort($set);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('media_links',$set);
		$r->renderResponse($tpl->fetch('collection/media.tpl'));
	}

	public function getArchiveUris($r)
	{
		$coll = $this->collection->ascii_id;
		$output = "#collection\n";
		$output .= $r->app_root.'/collection/'.$coll."/entry.atom\n";
		$output .= "#attributes\n";
		foreach ($this->collection->getAttributes() as $att) {
			$output .= $r->app_root.'/attribute/'.$coll.'/'.$att->ascii_id.".atom\n";
		}	
		$output .= "#item_types\n";
		foreach ($this->collection->getItemTypes() as $it) {
			$output .= $it->getUrl($this->collection->ascii_id,$r->app_root).".atom\n";
		}	
		$output .= "#items\n";
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			$output .= $item->getUrl($r->app_root).".atom\n";
		}	
		$r->renderResponse($output);

	}

	public function getAttributesUris($r) 
	{
		$coll = $this->collection->ascii_id;
		$output = '';
		foreach ($this->collection->getAttributes() as $att) {
			$output .= $r->app_root.'/attribute/'.$coll.'/'.$att->ascii_id;
			$output .= "\n";
		}
		$r->renderResponse($output);
	}

	public function getProfileJson($r)
	{
		$profile = array();
		$profile['id'] = $this->collection->ascii_id;
		$profile['name'] = $this->collection->collection_name;
		$item_types = array();
		$item_types['none'] = 'default/none';
		foreach ($this->collection->getItemTypes() as $it) {
			$item_types[$it->ascii_id] = $it->name;
		}
		$profile['item_types'] = $item_types;
		$attributes = array();
		foreach ($this->collection->getAttributes('attribute_name') as $att) {
			$att = clone($att);
			$attributes[$att->ascii_id] = $att->attribute_name;
		}
		$profile['attributes'] = $attributes;
		$r->renderResponse(Dase_Json::get($profile));
	}

	public function getItemTypesJson($r)
	{
		$types = array();
		//$default['ascii_id'] = 'none';
		$default['ascii_id'] = 'default';
		$default['name'] = 'default/none';
		$types[] = $default;
		foreach ($this->collection->getItemTypes() as $it) {
			$type['ascii_id'] = $it->ascii_id;
			$type['name'] = $it->name;
			$types[] = $type;
		}
		$r->renderResponse(Dase_Json::get($types));
	}

	public function getLastSerialNumberTxt($r)
	{
		$r->renderResponse($this->collection->getLastSerialNumber($r->get('begins_with')));
	}

	public function getEntryAtom($r)
	{
		$r->renderResponse($this->collection->asAtomEntry($r->app_root));
	}

	public function getItemTypesAtom($r)
	{
		$r->renderResponse($this->collection->getItemTypesAtom($r->app_root)->asXml());
	}

	public function getSerialNumbersTxt($r)
	{
		$r->checkCache();
		$sernums = $this->collection->getSerialNumbers();
		$r->renderResponse(join('|',$sernums));
	}

	public function getLatestIndex($r)
	{
		$search = new Dase_Solr($this->db,$this->config);
		$r->renderResponse($search->getLatestTimestamp($this->collection->ascii_id));
	}

	public function getItemsTxt($r) 
	{
		$output = '';
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			$output .= $item->serial_number; 
			//pass in 'display' params to view att value
			foreach ($r->get('display',true) as $member) {
				$output .= '|'.$item->getValue($member);
			}
			$output .= "\n";
		}
		$r->renderResponse($output);
	}

	public function getItemsUris($r) 
	{
		$output = '';
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			$output .= $item->getUrl($r->app_root); 
			$output .= "\n";
		}
		$r->renderResponse($output);
	}

	public function getItemsJson($r) 
	{
		$items = array();
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			$items[] = $item->asJson($r->app_root); 
		}
		$coll_url = $this->collection->getUrl($r->app_root);
		$updated = $this->collection->updated;
		$json = "{\"id\":\"$coll_url\",\"updated\":\"$updated\",\"items\":[";
		$json .= join(',',$items).']}';
		$r->renderResponse($json);
	}

	public function getDumpJson($r) 
	{
			$item_json = new Dase_DBO_ItemJson($this->db);
			$item_json->addWhere('unique_id',$this->collection->ascii_id.'/%','like');
			$docs = array();
			foreach ($item_json->find() as $ij) {
					$ij = clone($ij);
					$doc = str_replace('{APP_ROOT}',$r->app_root,$ij->doc);
					$docs[] = $doc;
			}
			$coll_url = $this->collection->getUrl($r->app_root);
			$updated = $this->collection->updated;
			$name = $this->collection->collection_name;
			$json = "{\"id\":\"$coll_url\",\"collection_name\":\"$name\",\"updated\":\"$updated\",\"items\":[";
			$json .= join(',',$docs).']}';
			$r->renderResponse($json);
	}

	public function getItemsByRangeAtom($r)
	{
		$r->renderResponse($this->collection->getItemsBySerialNumberRangeAsAtom($r->app_root,$r->get('start'),$r->get('end')));
	}

	public function getItemsThatLackMediaTxt($r) 
	{
		$output = '';
		$i = 0;
		$limit = '';
		if ($r->has('limit')) {
			$limit = $r->get('limit');
		}
		if ($r->has('count')) {
			$count = $r->get('count');
		} else {
			$count = 0;
		}
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			if ($item->getMediaCount() <= $count) {
				$i++;
				$output .= $item->serial_number; 
				//pass in 'display' params to view att value
				foreach ($r->get('display',true) as $member) {
					$output .= '|'.$item->getValue($member);
				}
				$output .= "\n";
			}
			if ($limit && $i == $limit) {
				break;
			}
		}
		if ($r->has('get_count')) {
			$output = $i;
		}
		$r->renderResponse($output);
	}

	public function getItemsThatLackMediaUris($r) 
	{
		$output = '';
		$i = 0;
		$limit = '';
		if ($r->has('limit')) {
			$limit = $r->get('limit');
		}
		if ($r->has('count')) {
			$count = $r->get('count');
		} else {
			$count = 0;
		}
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			if ($item->getMediaCount() <= $count) {
				$i++;
				//pass in 'display' params to view att value
				foreach ($r->get('display',true) as $member) {
					$output .= '#'.$item->getValue($member)."\n";
				}
				if ($r->get('showmedialink')) {
					//returns list of media links, not item links!!
					$output .= $item->getEditMediaUrl($r->app_root); 
				} else {
					$output .= $item->getUrl($r->app_root); 
				}
				$output .= "\n";
			}
			if ($limit && $i == $limit) {
				break;
			}
		}
		if ($r->has('get_count')) {
			$output = $i;
		}
		$r->renderResponse($output);
	}

	public function getItemsThatLackMediaJson($r) 
	{
		$items = array();
		$i = 0;
		$limit = '';
		if ($r->has('limit')) {
			$limit = $r->get('limit');
		}
		if ($r->has('count')) {
			$count = $r->get('count');
		} else {
			$count = 0;
		}
		foreach ($this->collection->getItems() as $item) {
			$item = clone($item);
			if ($item->getMediaCount() <= $count) {
				$i++;
				$items[] = $item->asJson($r->app_root); 
			}
			if ($limit && $i == $limit) {
				break;
			}
		}
		$coll_url = $this->collection->getUrl($r->app_root);
		$updated = $this->collection->updated;
		$json = "{\"id\":\"$coll_url\",\"updated\":\"$updated\",\"items\":[";
		$json .= join(',',$items).']}';
		$r->renderResponse($json);
	}

	public function getItemsMarkedToBeDeleted($r) 
	{
		$tpl = new Dase_Template($r);
		$url = str_replace('.html','',$r->url);
		$tpl->assign('items',Dase_Atom_Feed::retrieve($r->app_root.'/'.$url.'.atom'));
		$r->renderResponse($tpl->fetch('item_set/deletions.tpl'));
	}


	public function getItemsMarkedToBeDeletedAtom($r) 
	{
		$feed = new Dase_Atom_Feed;
		$feed->setTitle($this->collection->collection_name.' items to be deleted');
		$feed->setId(Dase_Atom::getNewId());
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->collection->id;
		$items->status = 'delete';
		foreach ($items->find() as $item) {
			$item = clone($item);
			$entry = $feed->addEntry();
			$entry->addLink($r->app_root.'/item/'.$this->collection->ascii_id.'/'.$item->serial_number,"http://daseproject.org/relation/search-item");
			$item->injectAtomEntryData($entry,$r->app_root);
		}
		$r->renderResponse($feed->asXml());
	}

	public function getItemsMarkedToBeDeletedTxt($r) 
	{
		$output = '';
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->collection->id;
		$items->status = 'delete';
		foreach ($items->find() as $item) {
			$output .= $item->serial_number.'|'; 
		}
		$r->renderResponse($output);
	}

	public function getItemsMarkedToBeDeletedUris($r) 
	{
		$output = '';
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->collection->id;
		$items->status = 'delete';
		foreach ($items->find() as $item) {
			$output .= $item->getUrl($r->app_root)."\n"; 
		}
		$r->renderResponse($output);
	}

	public function getItemsByMd5Txt($r) 
	{
		$file = new Dase_DBO_MediaFile($this->db);
		$file->md5 = $r->get('md5');
		$file->p_collection_ascii_id = $this->collection->ascii_id;
		if ($file->findOne()) {
			$r->renderResponse($file->p_serial_number.' is a duplicate');
		} else {
			//$r->renderError(404,'no item with checksum '.$r->get('md5'));
			$r->renderError(404);
		}
	}

	public function getItemsByMd5($r) 
	{
		$file = new Dase_DBO_MediaFile($this->db);
		$file->md5 = $r->get('md5');
		$file->p_collection_ascii_id = $this->collection->ascii_id;
		if ($file->findOne()) {
			$r->renderResponse($file->p_serial_number.' is a duplicate');
		} else {
			//$r->renderError(404,'no item with checksum '.$r->get('md5'));
			$r->renderError(404);
		}
	}

	public function getItemsByAttAtom($r)
	{
		$r->renderResponse($this->collection->getItemsByAttAsAtom($r->get('att_ascii_id'),$r->app_root));
	}

	public function getPing($r)
	{
		$r->renderResponse('ok');
	}

	public function getRecent($r)
	{
		//this is trickir than it seems (lovely RFC 3339)
	}

	public function getCollectionAtom($r) 
	{
		if ($r->has('limit')) {
		   $limit = $r->get('limit');
		} else {
			$limit = 5;
		}
		if ('entry' == $r->get('type')) {
			$r->renderResponse($this->collection->asAtomEntry($r->app_root));
		} else {
			$r->renderResponse($this->collection->asAtom($r->app_root,$limit));
		}
	}

	public function deleteCollection($r)
	{
		$user = $r->getUser('http');
		if (!$user->is_superuser) {
			$r->renderError(401,$user->eid.' is not permitted to delete a collection');
		}
		if ($this->collection->item_count < 5) {
			$this->collection->expunge();
			$r->renderResponse('delete succeeded',false,200);
		} else {
			$r->renderError(403,'cannot delete collection with more than 5 items');
		}
	}

	public function getCollection($r) 
	{
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',Dase_Atom_Feed::retrieve($r->app_root.'/collection/'.$r->get('collection_ascii_id').'.atom'));
		$r->renderResponse($tpl->fetch('collection/browse.tpl'));
	}

	public function postToAttributes($r) 
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'no go unauthorized');
		}
		$content_type = $r->getContentType();

		if ('application/atom+xml;type=entry' == $content_type ||
		'application/atom+xml' == $content_type ) {
			$this->_newAtomAttribute($r);
		} else {
			$r->renderError(415,'cannot accept '.$content_type);
		}
	}

	public function postToCommit($r) 
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'no go unauthorized');
		}
		$ds = new Dase_Solr($this->db,$this->config);
		if ('ok' == $ds->commit()) {
			$r->renderOk('commit successful');
		} else {
			$r->renderError(400,'did not commit');
		}
	}

	public function postToItemTypes($r) 
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'no go unauthorized');
		}
		$content_type = $r->getContentType();

		if ('application/atom+xml;type=entry' == $content_type ||
		'application/atom+xml' == $content_type ) {
			$this->_newAtomItemType($r);
		} else {
			$r->renderError(415,'cannot accept '.$content_type);
		}
	}

	public function postToCollection($r) 
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'no go unauthorized');
		}
		$content_type = $r->getContentType();

		if ('application/atom+xml;type=entry' == $content_type ||
		'application/atom+xml' == $content_type ) {
			$this->_newAtomItem($r);
		} elseif ('application/json' == $content_type) {
			$this->_newJsonItem($r);
		} else {
			$r->renderError(415,'cannot accept '.$content_type);
		}
	}

	public function putCollection($r)
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'cannot update collection');
		}
		$content_type = $r->getContentType();
		if ('application/atom+xml;type=entry' == $content_type ||
			'application/atom+xml' == $content_type
		) {
			$raw_input = $r->getBody();
			$client_md5 = $r->getHeader('Content-MD5');
			//if Content-MD5 header isn't set, we just won't check
			if ($client_md5 && md5($raw_input) != $client_md5) {
				$r->renderError(412,'md5 does not match');
			}
			try {
				$collection_entry = Dase_Atom_Entry::load($raw_input,'collection');
			} catch(Exception $e) {
				Dase_Log::debug(LOG_FILE,'collection handler error: '.$e->getMessage());
				$r->renderError(400,'bad xml');
			}
			if ('collection' != $collection_entry->entrytype) {
				//$collection_entry->setEntryType('collection');
				$r->renderError(400,'must be an collection entry');
			}
			$collection = $collection_entry->update($this->db,$r);
			if ($collection) {
				$r->renderOk('collection has been updated');
			} else {
				$r->renderError(500,'collection not updated');
			}
		} else {
			$r->renderError(415,'cannot accept '.$content_type);
		}
		$r->renderError(500,'an error occurred');
	}

	public function postToIngester($r) 
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'no go unauthorized');
		}
		$content_type = $r->getContentType();

		if ('application/atom+xml;type=entry' == $content_type ||
		'application/atom+xml' == $content_type ) {
			//will try to fetch enclosure
			$this->_newAtomItem($r,true);
		} elseif ('text/uri-list' == $content_type ) {
			$this->_newUriMediaResource($r);
		} elseif ('application/json' == $content_type ) {
			$this->_newJsonMediaResource($r);
		} else {
			$r->renderError(415,'cannot accept '.$content_type);
		}
	}

	private function _newJsonMediaResource($r)
	{
		$eid = $r->getUser('http')->eid;
		$json = $r->getBody();
		$set_data = Dase_Json::toPhp($json);
		//single item or set
		if (!isset($set_data['items'])) {
			$set = array();
			$set['items'] = array($set_data);
			$set_data = $set;
		}
		$num = 0;
		foreach ($set_data['items'] as $set_item) {
			$item = $this->collection->createNewItem(null,$eid);
			foreach ($set_item['metadata'] as $att => $val_set) {
				foreach ($val_set as $val) {
					$item->setValue($att,$val);
				}
			}
			if (isset($set_item['enclosure'])) {
				$url = $set_item['enclosure']['href'];
				if ('/' == substr($url,0,1)) {
					$url = $set_item['app_root'].$url;
				}
				$mime = $set_item['enclosure']['type'];
				$ext = Dase_File::getExtension($mime);
				if (!$ext) {
					continue;
				}
				$upload_dir = MEDIA_DIR.'/'.$this->collection->ascii_id.'/uploaded_files';
				if (!file_exists($upload_dir)) {
					$r->renderError(500,'missing upload directory');
				}
				$new_file = $upload_dir.'/'.$item->serial_number.'.'.$ext;
				file_put_contents($new_file,file_get_contents($url));
				try {
					$file = Dase_File::newFile($this->db,$new_file,null,null,BASE_PATH);
					//$media_file = $file->addToCollection($item,true,MEDIA_DIR); //check for dups
					//accept dups
					$media_file = $file->addToCollection($item,false,MEDIA_DIR); 
					$item->mapConfiguredAdminAtts();
					$item->buildSearchIndex();
					$num++;
				} catch(Exception $e) {
					Dase_Log::debug(LOG_FILE,'coll handler error: '.$e->getMessage());
					$item->expunge();
					$r->renderError(409,'could not ingest uri resource ('.$e->getMessage().')');
				}
			}
		}
		$r->renderResponse('ingested '.$num.' items');
	}

	private function _newUriMediaResource($r)
	{
		$eid = $r->getUser('http')->eid;
		$url = $r->getBody();
		$filename = array_pop(explode('/',$url));
		$ext = array_pop(explode('.',$url));
		$upload_dir = MEDIA_DIR.'/'.$this->collection->ascii_id.'/uploaded_files';
		if (!file_exists($upload_dir)) {
			$r->renderError(500,'missing upload directory');
		}
		$item = $this->collection->createNewItem(null,$eid);
		$item->setValue('title',urldecode($filename));
		$new_file = $upload_dir.'/'.$item->serial_number.'.'.$ext;
		file_put_contents($new_file,file_get_contents($url));
		try {
			$file = Dase_File::newFile($this->db,$new_file,null,null,BASE_PATH);
			//$media_file = $file->addToCollection($item,true,MEDIA_DIR); //check for dups
			//accept dups
			$media_file = $file->addToCollection($item,false,MEDIA_DIR); //check for dups
			$item->mapConfiguredAdminAtts();
			$item->buildSearchIndex();
		} catch(Exception $e) {
			Dase_Log::debug(LOG_FILE,'coll handler error: '.$e->getMessage());
			$item->expunge();
			$r->renderError(409,'could not ingest uri resource ('.$e->getMessage().')');
		}
		header("HTTP/1.1 201 Created");
		header("Content-Type: text/plain");
		header("Location: ".$r->app_root."/item/".$r->get('collection_ascii_id')."/".$item->serial_number);
		echo $filename;
		exit;
	}

	private function _newAtomItem($r,$fetch_enclosure=false)
	{
		$raw_input = $r->getBody();
		$client_md5 = $r->getHeader('Content-MD5');
		//if Content-MD5 header isn't set, we just won't check
		if ($client_md5 && md5($raw_input) != $client_md5) {
			$r->renderError(412,'md5 does not match');
		}
		try {
			$item_entry = Dase_Atom_Entry::load($raw_input,'item');
		} catch(Exception $e) {
			Dase_Log::debug(LOG_FILE,'coll handler error: '.$e->getMessage());
			$r->renderError(400,'bad xml');
		}
		if ('item' != $item_entry->entrytype) {
			$item_entry->setEntryType('item');
			$r->renderError(400,'must be an item entry');
		}
		try {
			$item = $item_entry->insert($this->db,$r,$fetch_enclosure);
			header("HTTP/1.1 201 Created");
			header("Content-Type: application/atom+xml;type=entry;charset='utf-8'");
			header("Location: ".$r->app_root."/item/".$r->get('collection_ascii_id')."/".$item->serial_number.'.atom');
			echo $item->asAtomEntry($r->app_root);
			exit;
		} catch (Dase_Exception $e) {
			$r->renderError(409,$e->getMessage());
		}
	}

	private function _newAtomAttribute($r)
	{
		$raw_input = $r->getBody();
		$client_md5 = $r->getHeader('Content-MD5');
		//if Content-MD5 header isn't set, we just won't check
		if ($client_md5 && md5($raw_input) != $client_md5) {
			$r->renderError(412,'md5 does not match');
		}
		try {
			$att_entry = Dase_Atom_Entry::load($raw_input);
		} catch(Exception $e) {
			Dase_Log::debug(LOG_FILE,'coll handler error: '.$e->getMessage());
			$r->renderError(400,'bad xml');
		}
		if ('attribute' != $att_entry->entrytype) {
			$att_entry->setEntryType('attribute');
			$r->renderError(400,'must be an attribute entry');
		}
		try {
			$att = $att_entry->insert($this->db,$r,$this->collection);
			header("HTTP/1.1 201 Created");
			header("Content-Type: application/atom+xml;type=entry;charset='utf-8'");
			header("Location: ".$r->app_root."/attribute/".$r->get('collection_ascii_id')."/".$att->ascii_id.'.atom');
			echo $att->asAtomEntry($this->collection->ascii_id,$r->app_root);
			exit;
		} catch (Dase_Exception $e) {
			$r->renderError(409,$e->getMessage());
		}
	}

	private function _newAtomItemType($r)
	{
		$raw_input = $r->getBody();
		$client_md5 = $r->getHeader('Content-MD5');
		//if Content-MD5 header isn't set, we just won't check
		if ($client_md5 && md5($raw_input) != $client_md5) {
			$r->renderError(412,'md5 does not match');
		}
		try {
			$type_entry = Dase_Atom_Entry::load($raw_input);
		} catch(Exception $e) {
			Dase_Log::debug(LOG_FILE,'coll handler error: '.$e->getMessage());
			$r->renderError(400,'bad xml');
		}
		if ('item_type' != $type_entry->entrytype) {
			$r->renderError(400,'must be an item type entry');
		}
		try {
			$item_type = $type_entry->insert($this->db,$r,$this->collection);
			header("HTTP/1.1 201 Created");
			header("Content-Type: application/atom+xml;type=entry;charset='utf-8'");
			header("Location: ".$r->app_root."/item_type/".$r->get('collection_ascii_id')."/".$item_type->ascii_id.'.atom');
			echo $type->asAtomEntry($this->collection->ascii_id,$r->app_root);
			exit;
		} catch (Dase_Exception $e) {
			$r->renderError(409,$e->getMessage());
		}
	}

	private function _newJsonItem($r)
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->collection)) {
			$r->renderError(401,'no go unauthorized');
		}
		$json = $r->getBody();
		$client_md5 = $r->getHeader('Content-MD5');
		//if Content-MD5 header isn't set, we just won't check
		if ($client_md5 && md5($json) != $client_md5) {
			$r->renderError(412,'md5 does not match');
		}
		$slug = $r->slug ? $r->slug : ''; 
		$sernum = Dase_Util::makeSerialNumber($slug);
		try {
			$item = $this->collection->createNewItem($sernum,$user->eid);
			$item_data = Dase_Json::toPhp($json);

			//item type
			if (isset($item_data['item_type'])) {
					$item->setItemType($item_data['item_type']);
			}
			$metadata = $item_data['metadata'];
			foreach ($metadata as $key => $vals) {
				foreach ($vals as $val) {
					$item->setValue($key,$val);
				}
			}
			$item->buildSearchIndex();
			header("HTTP/1.1 201 Created");
			header("Content-Type: application/atom+xml;type=entry;charset='utf-8'");
			header("Location: ".$r->app_root."/item/".$r->get('collection_ascii_id')."/".$item->serial_number.'.atom');
			echo $item->asAtomEntry($r->app_root);
			exit;
		} catch (Dase_Exception $e) {
			$r->renderError(409,$e->getMessage());
		}
	}

	public function getAttributesAtom($r) 
	{
		$r->renderResponse($this->collection->getAttributesAtom($r->app_root)->asXml());
	}

	public function getAttributesJson($r) 
	{
		$filter = $r->has('filter') ? $r->get('filter') : '';
		$r->checkCache();
		$c = $this->collection;
		$attributes = new Dase_DBO_Attribute($this->db);
		$attributes->collection_id = $c->id;
		if ('public' == $filter) {
			$attributes->is_public = true;
		}
		if ($r->has('sort')) {
			$so = $r->get('sort');
		} else {
			//$so = 'sort_order';
			$so = 'attribute_name';
		}
		$attributes->orderBy($so);
		//$attributes->orderBy('attribute_name');
		$att_array = array();
		foreach($attributes->find() as $att) {
			$att_array[] =
				array(
					'id' => $att->id,
					'ascii_id' => $att->ascii_id,
					'attribute_name' => $att->attribute_name,
					'input_type' => $att->html_input_type,
					'sort_order' => $att->sort_order,
					'href' => $att->getUrl($c->ascii_id,$r->app_root),
					'collection' => $c->ascii_id,
					'modifier_type' => $att->modifier_type,
					'modifier_defined_list' => $att->modifier_defined_list,
					'values' => $att->getFormValues(),
					'usage_notes' => $att->usage_notes,
				);
		}
		$r->renderResponse(Dase_Json::get($att_array),$r);
	}

	public function getAdminAttributesJson($r) 
	{
		$r->checkCache();
		$c = $this->collection;
		$attributes = new Dase_DBO_Attribute($this->db);
		$attributes->collection_id = 0;
		$attributes->orderBy('attribute_name');
		$att_array = array();
		foreach($attributes->find() as $att) {
			$att_array[] =
				array(
					'id' => $att->id,
					'ascii_id' => $att->ascii_id,
					'attribute_name' => $att->attribute_name,
					'input_type' => $att->html_input_type,
					'sort_order' => $att->sort_order,
					'collection' => $r->get('collection_ascii_id')
				);
		}
		$r->renderResponse(Dase_Json::get($att_array),$r);
	}

	public function getAttributeTalliesJson($r) 
	{
		$prefix = $this->db->table_prefix;
		//todo: work on cacheing here
		//$r->checkCache(1500);
		$c = $this->collection;
		$dbh = $this->db->getDbh();
		$sql = "
			SELECT id, ascii_id
			FROM {$prefix}attribute a
			WHERE a.collection_id = ?
			AND a.is_public = true;
		";
		$sth1 = $dbh->prepare($sql);
		$sth1->execute(array($c->id));
		$sql = "
			SELECT count(DISTINCT value_text) 
			FROM {$prefix}value 
			WHERE attribute_id = ?";
		$sth2 = $dbh->prepare($sql);
		$tallies = array();
		while ($row = $sth1->fetch()) {
			$sth2->execute(array($row['id']));
			$tallies[$row['ascii_id']] = $sth2->fetchColumn();
		}
		$result['tallies'] = $tallies;
		$result['is_admin'] = 0;
		$r->renderResponse(Dase_Json::get($result));
	}

	public function getAdminAttributeTalliesJson($r) 
	{
		$prefix = $this->db->table_prefix;
		$c = $this->collection;
		$dbh = $this->db->getDbh();
		$sql = "
			SELECT id, ascii_id
			FROM {$prefix}attribute a
			WHERE a.collection_id = 0
			";
		$sth1 = $dbh->prepare($sql);
		$sth1->execute();
		$sql = "
			SELECT count(DISTINCT value_text) 
			FROM {$prefix}value v, {$prefix}item i
			WHERE v.attribute_id = ?
			AND v.item_id = i.id
			AND i.collection_id = ? 
			";
		$sth2 = $dbh->prepare($sql);
		$tallies = array();
		while ($row = $sth1->fetch()) {
			$sth2->execute(array($row['id'],$c->id));
			$tallies[$row['ascii_id']] = $sth2->fetchColumn();
		}
		$result['tallies'] = $tallies;
		$result['is_admin'] = 1;
		$r->renderResponse(Dase_Json::get($result));
	}

	public function getServiceAtom($r)
	{
		$this->getService($r);
	}

	public function getServiceTxt($r)
	{
		$this->getService($r);
	}

	public function getService($r)
	{
		$r->response_mime_type = 'application/atomsvc+xml';
		$r->renderResponse($this->collection->getAtompubServiceDoc($r->app_root));
	}
}

