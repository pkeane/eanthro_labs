<?php

require_once 'Dase/DBO/Autogen/Collection.php';

class Dase_DBO_Collection extends Dase_DBO_Autogen_Collection
{
	public static function get($db,$ascii_id)
	{
		if (!$ascii_id) {
			throw new Exception('missing collection ascii id');
		}
		$collection = new Dase_DBO_Collection($db);
		$collection->ascii_id = $ascii_id;
		if ($collection->findOne()) {
			return $collection;
		} else {
			return false;
		}
	}

	public function getLastSerialNumber($begins_with)
	{
		$item = new Dase_DBO_Item($this->db);
		$item->collection_id = $this->id;
		$item->orderBy('serial_number DESC');
		if (false !== $begins_with) {
			$item->addWhere('serial_number',$begins_with.'%','like');
		}
		if ($item->findOne()) {
			return $item->serial_number;
		} else {
			return false;
		}
	}


	public function getUrl($app_root) {
		return $app_root.'/collection/' . $this->ascii_id;
	}

	public function getMediaUrl($app_root) {
		return $app_root.'/media/' . $this->ascii_id;
	}

	public function createAscii() {
		if (!$this->collection_name) {
			return false;
		}
		$ascii_id = trim(preg_replace('/(collection|archive)/i','',$this->collection_name));
		$ascii_id = preg_replace('/ /i',"_",$ascii_id);
		$ascii_id = strtolower(preg_replace('/(__|_$)/','',$ascii_id));
		return $ascii_id;
	}

	/** called reduce since empty is reserved */
	public function reduce($messages = false)
	{
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->id;
		foreach ($items->find() as $item) {
			Dase_Log::info(LOG_FILE,"item $this->ascii_id:$item->serial_number deleted");
			if ($messages) {
				print "item $this->ascii_id:$item->serial_number deleted\n";
			}
			$item->expunge();
		}
	}

	public function expunge($messages = false)
	{
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->id;
		foreach ($items->find() as $item) {
			Dase_Log::info(LOG_FILE,"item $this->ascii_id:$item->serial_number deleted");
			if ($messages) {
				print "item $this->ascii_id:$item->serial_number deleted\n";
			}
			$item->expunge();
		}
		$item_types = new Dase_DBO_ItemType($this->db);
		$item_types->collection_id = $this->id;
		foreach ($item_types->find() as $type) {
			$type->expunge();
		}

		$atts = new Dase_DBO_Attribute($this->db);
		$atts->collection_id = $this->id;
		foreach ($atts->find() as $a) {
			$a->delete();
		}	

		$cms = new Dase_DBO_CollectionManager($this->db);
		$cms->collection_ascii_id = $this->ascii_id;
		foreach ($cms->find() as $cm) {
			$cm->delete();
		}
		$this->delete();
		Dase_Log::info(LOG_FILE,"$this->ascii_id deleted");
		if ($messages) {
			print "$this->ascii_id collection deleted\n";
		}
	}

	function getSerialNumbers()
	{
		$sernums = array();
		foreach ($this->getItems() as $item) {
			$sernums[] = $item->serial_number;
		}
		return $sernums;
	}

	function getBaseAtomFeed($app_root) 
	{
		$feed = new Dase_Atom_Feed;
		$feed->setTitle($this->collection_name);
		if ($this->description) {
			$feed->setSubtitle($this->description);
		}
		$feed->setUpdated($this->updated);
		$feed->addCategory($app_root,"http://daseproject.org/category/base_url");
		$feed->addCategory($this->item_count,"http://daseproject.org/category/item_count");
		//todo: is this too expensive??
		$feed->setId($this->getUrl($app_root));
		$feed->addAuthor();
		$feed->addLink($this->getUrl($app_root),'alternate');
		$feed->addLink($this->getUrl($app_root).'/service','service','application/atomsvc+xml',null,'AtomPub Service Document');
		return $feed;
	}

	function getAttributesAtom($app_root) {
		$feed = $this->getBaseAtomFeed($app_root);
		$feed->setFeedType('attributes');
		foreach ($this->getAttributes() as $att) {
			$att->injectAtomEntryData($feed->addEntry(),$this->ascii_id,$app_root);
		}
		return $feed;
	}

	function getItemTypesAtom($app_root) {
		$feed = new Dase_Atom_Feed;
		$feed->setTitle($this->collection_name.' Item Types');
		$feed->setUpdated($this->updated);
		$feed->setId($this->getUrl($app_root));
		$feed->addAuthor();
		$feed->addCategory($app_root,"http://daseproject.org/category/base_url");
		$feed->addLink($this->getUrl($app_root),'alternate');
		$feed->addLink($this->getUrl($app_root).'/service','service','application/atomsvc+xml',null,'AtomPub Service Document');
		$feed->setFeedType('item_types');
		foreach ($this->getItemTypes() as $it) {
			$it->injectAtomEntryData($feed->addEntry(),$this->ascii_id,$app_root);
		}
		return $feed;
	}

	function asAtom($app_root,$limit = 5)
	{
		$feed = $this->getBaseAtomFeed($app_root);
		$feed->setFeedType('collection');
		$feed->addLink($app_root.'/collection/'.$this->ascii_id.'.atom','self');
		$feed->addCategory($app_root,"http://daseproject.org/category/base_url");
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->id;
		if ($limit && is_numeric($limit)) {
			$items->setLimit($limit);
		}
		$items->orderBy('updated DESC');
		foreach ($items->find() as $item) {
			$feed->addItemEntry($item,$app_root);
		}
		return $feed->asXml();
	}

	function getItemsUpdatedSince($datetime,$limit=100)
	{
		//normalize timezone and make allowance for clock drift
		//note: if more than 30 second drift, records could fall through cracks
		$datetime = date(DATE_ATOM,strtotime($datetime)-30);
		$set = array();
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->id;
		$items->addWhere('updated',$datetime,'>');
		$items->orderBy('updated DESC');
		if ($limit && is_numeric($limit)) {
			$items->setLimit($limit);
		}
		foreach ($items->find() as $item) {
			$set[] = clone $item;
		}
		return $set;
	}

	function getItemsBySerialNumberRangeAsAtom($app_root,$start,$end)
	{
		$feed = $this->getBaseAtomFeed($app_root);
		$feed->setFeedType('collection');
		$feed->addLink($app_root.'/collection/'.$this->ascii_id.'/items/range/'.$start.'/'.$end.'.atom','self');
		$feed->addCategory($app_root,"http://daseproject.org/category/base_url");
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->id;
		$items->addWhere('serial_number',$start,'>=');
		$items->addWhere('serial_number',$end,'<=');
		$items->setLimit(100);
		$items->orderBy('updated DESC');
		foreach ($items->find() as $item) {
			$feed->addItemEntry($item,$app_root);
		}
		return $feed->asXml();
	}

	function asAtomEntry($app_root)
	{
		$entry = new Dase_Atom_Entry();
		return $this->injectAtomEntryData($entry,$app_root)->asXml();
	}

	function injectAtomEntryData(Dase_Atom_Entry $entry,$app_root)
	{
		$url = $this->getUrl($this->ascii_id,$app_root);
		$entry->setTitle($this->collection_name);
		$entry->setId($url);
		$entry->setUpdated($this->updated);
		$entry->addAuthor();
		$entry->addLink($url.'.atom');
		$entry->addLink($url.'.atom','edit');
		$entry->addCategory('collection','http://daseproject.org/category/entrytype');
		if ($this->description) {
			$entry->setSummary($this->description);
		} else {
			$entry->setSummary(str_replace('_collection','',$this->ascii_id));
		}

		if ($this->admin_notes) {
			$entry->setContent($this->admin_notes);
		}

		foreach ($this->getAttributes() as $a) {
			$entry->addCategory($a->ascii_id,"http://daseproject.org/category/attribute",$a->attribute_name);
		}
		foreach ($this->getItemTypes() as $item_type) {
			$entry->addCategory($item_type->ascii_id,"http://daseproject.org/category/item_type",$item_type->name);
		}

		if ($this->is_public) {
			$pub = "public";
		} else {
			$pub = "private";
		}
		$entry->addCategory($pub,"http://daseproject.org/category/status");

		if (!$this->visibility) {
			$this->visibility = 'user';
		}
		$entry->addCategory($this->visibility,"http://daseproject.org/category/visibility");
		$entry->addCategory($app_root,"http://daseproject.org/category/base_url");
		$entry->addCategory($this->item_count,"http://daseproject.org/category/item_count");
		return $entry;
	}

	static function listAsJson($db,$public_only = false,$app_root)
	{
		$colls = new Dase_DBO_Collection($db);
		$colls->orderBy('collection_name');
		if ($public_only) {
			$colls->is_public = 1;
		} 
		foreach ($colls->find() as $c) {
			$coll_array = array();
			$coll_array['id'] = $app_root.'/collection/'.$c->ascii_id;
			foreach ($c as $k => $v) {
				$coll_array[$k] = $v;
			}
			$coll_array['links'] = array('alternate' => $app_root.'/collection/'.$c->ascii_id);
			$coll_array['title'] = $c->collection_name;
			$result[] = $coll_array;
		}
		return Dase_Json::get($result);
	}

	function asJson($app_root)
	{
		$coll_array = array();
		$coll_array['id'] = $app_root.'/collection/'.$this->ascii_id;
		foreach ($this as $k => $v) {
			$coll_array[$k] = $v;
		}
		$coll_array['links'] = array('alternate' => $app_root.'/collection/'.$this->ascii_id);
		$coll_array['title'] = $this->collection_name;
		return Dase_Json::get($coll_array);
	}

	static function listAsAtom($db,$app_root,$public_only = false)
	{
		$c = new Dase_DBO_Collection($db);
		$c->orderBy('collection_name');
		if ($public_only) {
			$c->is_public = 1;
		} 
		$cs = $c->find();
		$feed = new Dase_Atom_Feed;
		$feed->setTitle('DASe Collections');
		$feed->setId($app_root);
		$feed->setFeedType('collection_list');
		$feed->setUpdated(date(DATE_ATOM));
		$feed->addAuthor('DASe (Digital Archive Services)','http://daseproject.org');
		$feed->addLink($app_root.'/collections.atom','self');
		$feed->addCategory($app_root,"http://daseproject.org/category/base_url");
		foreach ($cs as $coll) {
			$coll->injectAtomEntryData($feed->addEntry(),$app_root);
		}
		return $feed->asXml();
	}

	static function getLookupArray($db)
	{
		$hash = array();
		$c = new Dase_DBO_Collection($db);
		foreach ($c->find() as $coll) {
			$iter = $coll->getIterator();
			foreach ($iter as $field => $value) {
				$coll_hash[$field] = $value;
			}
			$hash[$coll->id] = $coll_hash;
		}
		return $hash;
	}

	/**  note: this returns an array of manager (stdClass) objects
	 */
	function getManagers()
	{
		$prefix = $this->db->table_prefix;
		$sql = "
			SELECT m.dase_user_eid,m.auth_level,m.expiration,m.created,m.created_by_eid,u.name 
			FROM {$prefix}collection_manager m,{$prefix}dase_user u 
			WHERE m.collection_ascii_id = ?
			AND m.dase_user_eid = u.eid
			ORDER BY m.dase_user_eid";
		$dbh = $this->db->getDbh();
		$sth = $dbh->prepare($sql);
		$sth->setFetchMode(PDO::FETCH_OBJ);
		$sth->execute(array($this->ascii_id));
		return $sth;
	}

	function getAttributes($sort = 'sort_order')
	{
		$att = new Dase_DBO_Attribute($this->db);
		$att->collection_id = $this->id;
		$att->orderBy($sort);
		return $att->find();
	}

	public function resortAttributesByName() 
	{
		$new_sort_order = 0;
		foreach ($this->getAttributes('attribute_name') as $att) {
			$new_sort_order++;
			$att->sort_order = $new_sort_order;
			$att->fixBools();
			$att->update();
		}
	}

	function getAttributesSortedArray($sort = 'sort_order')
	{
		$att_array[] = 'First';
		foreach ($this->getAttributes($sort) as $att) {
			$att_array[] = "after ".$att->attribute_name;
		}
		return $att_array;
	}

	function getAdminAttributes()
	{
		$att = new Dase_DBO_Attribute($this->db);
		$att->collection_id = 0;
		$att->orderBy('sort_order');
		return $att->find();
	}

	/** this is NOT visibility, it is public/private */ 
	function updateVisibility($visibility)
	{
		if ('public' == $visibility) {
			$this->is_public = 1;
			$this->update();
		}
		if ('private' == $visibility) {
			$this->is_public = 0;
			$this->update();
		}
	}

	function updateItemCount()
	{
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->id;
		$this->item_count = $items->findCount();
		$this->updated = date(DATE_ATOM);
		//postgres boolean weirdness make this necessary
		if (!$this->is_public) {
			$this->is_public = 0;
		}
		$this->update();
	}

	function getItems($limit='')
	{
		$item = new Dase_DBO_Item($this->db);
		$item->collection_id = $this->id;

		//reverse chronological so we can get "recent" w/ limit param
		$item->orderBy('updated DESC');

		if ($limit && is_numeric($limit)) {
			$item->setLimit($limit);
		}
		//note: MUST clone items 
		return $item->find();
	}

	function getItemTypes()
	{
		$res = array();
		$types = new Dase_DBO_ItemType($this->db);
		$types->collection_id = $this->id;
		$types->orderBy('name');
		foreach ($types->find() as $t) {
			$res[] = clone $t;
		}
		return $res;
	}

	public function buildSearchIndex($since)
	{
		$set = $this->getItemsUpdatedSince($since);
		$item = null;
		foreach ($set as $item) {
			$item->buildSearchIndex(false);
		}
		//now commit
		if ($item) {
			$item->buildSearchIndex(true);
		}
		return count($set);
	}

	public function getItemsByAttAsAtom($attribute_ascii_id,$app_root)
	{
		$feed = $this->getBaseAtomFeed($app_root);
		$feed->setFeedType('items');
		$att = Dase_DBO_Attribute::get($this->db,$this->ascii_id,$attribute_ascii_id);
		$vals = new Dase_DBO_Value($this->db);
		$vals->attribute_id = $att->id;
		foreach ($vals->find() as $val) {
			$item = new Dase_DBO_Item($this->db);
			$item->load($val->item_id);
			//use cached ???
			$entry = $item->injectAtomEntryData($feed->addEntry(),$app_root);
			$entry->setSummary($item->getValue($attribute_ascii_id));
		}
		return $feed->asXML($app_root);
	}

	function createNewItem($serial_number=null,$eid=null)
	{
		if (!$eid) {
			$eid = '_dase';
		}
		$item = new Dase_DBO_Item($this->db);
		$item->collection_id = $this->id;
		if ($serial_number) {
			$item->serial_number = $serial_number;
			if ($item->findOne()) {
				Dase_Log::info(LOG_FILE,"duplicate serial number: ".$serial_number);
				throw new Dase_Exception('duplicate serial number!');
				return;
			}
			$item->status = 'public';
			$item->item_type_id = 0;
			$item->item_type_ascii_id = 'default';
			$item->item_type_name = 'default';
			$item->created = date(DATE_ATOM);
			$item->updated = date(DATE_ATOM);
			$item->created_by_eid = $eid;
			$item->p_collection_ascii_id = $this->ascii_id;
			$item->p_remote_media_host = $this->remote_media_host;
			$item->collection_name = $this->collection_name;
			$item->insert();
			$this->updateItemCount();
			return $item;
		} else {
			$item->status = 'public';
			$item->item_type_id = 0;
			$item->item_type_ascii_id = 'default';
			$item->item_type_name = 'default';
			$item->created = date(DATE_ATOM);
			$item->created_by_eid = $eid;
			$item->p_collection_ascii_id = $this->ascii_id;
			$item->p_remote_media_host = $this->remote_media_host;
			$item->collection_name = $this->collection_name;
			$item->insert();
			//after move to mysql to avoid collisions w/ old sernums
			//replace first '0' w/ '1'
			//todo: better way to generate unique sernum.
			// (do NOT forget to enforce uniqueness in DB)
			//$item->serial_number = sprintf("%09d",$item->id);
			$item->serial_number = '1'.sprintf("%08d",$item->id);
			$item->updated = date(DATE_ATOM);
			$item->update();
			$this->updateItemCount();
			return $item;
		}
	}

	public function getAtompubServiceDoc($app_root) 
	{
		$svc = new Dase_Atom_Service;	
		$ws = $svc->addWorkspace($this->collection_name.' Workspace');
		$coll = $ws->addCollection($app_root.'/collection/'.$this->ascii_id.'.atom',$this->collection_name.' Items');
		$coll->addAccept('application/atom+xml;type=entry');
		$coll->addCategorySet()->addCategory('item','http://daseproject.org/category/entrytype');
		$atts = $coll->addCategorySet('yes','http://daseproject.org/category/metadata');
		foreach ($this->getAttributes() as $att) {
			$atts->addCategory($att->ascii_id,'',$att->attribute_name);
		}
		$media_repos = $app_root.'/media/'.$this->ascii_id.'.atom';
		$media_coll = $ws->addCollection($media_repos,$this->collection_name.' Media');
		foreach(Dase_Media::getAcceptedTypes() as $type) {
			//$media_coll->addAccept($type,true);
			$media_coll->addAccept($type);
		}
		//json items collection
		$ws->addCollection($app_root.'/collection/'.$this->ascii_id.'.atom',$this->collection_name.' JSON Items')
			->addAccept('application/json');
		$item_types_repos = $app_root.'/collection/'.$this->ascii_id.'/item_types.atom';
		$ws->addCollection($item_types_repos,$this->collection_name.' Item Types')
			->addAccept('application/atom+xml;type=entry')
			->addCategorySet()
			->addCategory('item_type','http://daseproject.org/category/entrytype');
		$attributes_repos = $app_root.'/collection/'.$this->ascii_id.'/attributes.atom';
		$atts_repos = $ws->addCollection($attributes_repos,$this->collection_name.' Attributes');
		$atts_repos->addAccept('application/atom+xml;type=entry')->addCategorySet()
			->addCategory('attribute','http://daseproject.org/category/entrytype','',true);
		$html_inp_types = $atts_repos->addAccept('application/atom+xml;type=entry')
			->addCategorySet('yes','http://daseproject.org/category/html_input_type');
		foreach (array('text','textarea','select','radio','checkbox','noedit','list') as $inp) {
			$html_inp_types->addCategory($inp);
		}
		return $svc->asXml();
	}
}
