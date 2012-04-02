<?php

require_once 'Dase/DBO/Autogen/Tag.php';

class Dase_DBO_Tag extends Dase_DBO_Autogen_Tag 
{
	private $user;

	public static function searchTagsAtom($db,$app_root,$q)
	{
		//looks for q in title
		//public ONLY!!!!!!
		$feed = new Dase_Atom_Feed;
		$feed->setId($app_root.'/sets');
		$feed->setFeedType('sets');
		$feed->setUpdated(date(DATE_ATOM));
		$feed->addAuthor();

		$feed->setTitle('Public Sets search: '.$q);
		$tags = new Dase_DBO_Tag($db);
		$tags->is_public = true;
		$tags->addWhere('name',"%$q%",$db->getCaseInsensitiveLikeOp());
		$tags->orderBy('updated DESC');
		foreach ($tags->find() as $tag) {
			$tag = clone $tag;
			if (!$tag->item_count) {
				$tag->updateItemCount();
			}
			if ($tag->ascii_id) { //compat: make sure tag has ascii_id
				$entry = $tag->injectAtomEntryData($feed->addEntry('set'),null,$app_root);
				$entry->addCategory($tag->item_count,"http://daseproject.org/category/item_count");
			}
		}
		$feed->sortByTitle();
		return $feed->asXml();
	}

	public static function listAsFeed($db,$app_root,$category='')
	{
		//public ONLY!!!!!!
		$feed = new Dase_Atom_Feed;
		$feed->setId($app_root.'/sets');
		$feed->setFeedType('sets');
		$feed->setUpdated(date(DATE_ATOM));
		$feed->addAuthor();

		if ($category) {
			$scheme = '';
			$parts = explode('}',$category);
			if (1 == count($parts)) {
				$term = $parts[0];
			} elseif (2 == count($parts)) {
				$scheme = urldecode(trim($parts[0],'{'));
				$scheme = str_replace('http://daseproject.org/category/','',$scheme);
				$term = $parts[1];
			} else {
				return $feed->asXml();
			}
			/***************newly refactored*******************/

			 	$tag_cat = new Dase_DBO_TagCategory($db);
				$tag_cat->term = $term;
				$tag_cat->scheme = $scheme;
				$in_set = array();
				$category_label = $term;
				foreach ($tag_cat->find() as $tc) {
					$in_set[] = $tc->tag_id;
					$category_label = $tc->label;
				}
				$feed->setTitle('Sets for '.$category_label);
				

				$tags = new Dase_DBO_Tag($db);
				$tags->is_public = true;
				$tags->orderBy('updated DESC');
				foreach ($tags->find() as $tag) {
					$tag = clone $tag;
					if (!$tag->item_count) {
						$tag->updateItemCount();
					}
					if ($tag->ascii_id and in_array($tag->id,$in_set)) {
						$entry = $tag->injectAtomEntryData($feed->addEntry('set'),null,$app_root);
						$entry->addCategory($tag->item_count,"http://daseproject.org/category/item_count");
					}
				}

			 /*********************************************/

		} else {
			$feed->setTitle('All Public Sets');
			$tags = new Dase_DBO_Tag($db);
			$tags->is_public = true;
			$tags->orderBy('updated DESC');
			foreach ($tags->find() as $tag) {
				$tag = clone $tag;
				if (!$tag->item_count) {
					$tag->updateItemCount();
				}
				if ($tag->ascii_id) { //compat: make sure tag has ascii_id
					$entry = $tag->injectAtomEntryData($feed->addEntry('set'),null,$app_root);
					$entry->addCategory($tag->item_count,"http://daseproject.org/category/item_count");
				}
			}
		}
		$feed->sortByTitle();
		return $feed->asXml();
	}

	public static function getTagCategoriesByScheme($db,$app_root,$scheme)
	{
		$cats = new Dase_Atom_Categories();
		$cats->setScheme('http://daseproject.org/category/'.$scheme);

		$prefix = $db->table_prefix;
		$sql = "
			SELECT term, label 
			FROM {$prefix}tag_category 
			WHERE scheme = ?
			GROUP BY term, label
			ORDER BY label
			";
		$dbh = $db->getDbh();
		$sth = $dbh->prepare($sql);
		$sth->execute(array($scheme));
		foreach ($sth->fetchAll() as $row) { 
			$cats->addCategory($row['term'],'',$row['label']);
		}
		return $cats->asXml();
	}

	public static function create($db,$tag_name,$user)
	{
		if (!$tag_name) { return false; }
		$tag = new Dase_DBO_Tag($db);
		$tag->ascii_id = Dase_Util::dirify($tag_name);
		$tag->dase_user_id = $user->id;
		if ($tag->findOne()) {
			return false;
		} else {
			$tag->name = $tag_name;
			$tag->type = 'set';
			$tag->background = 'white';
			$tag->is_public = 0;
			$tag->item_count = 0;
			$tag->eid = $user->eid;
			$tag->created = date(DATE_ATOM);
			$tag->insert();
			return $tag;
		}
	}

	public static function get($db,$ascii_id,$eid)
	{
		if (!$ascii_id || !$eid) {
			return false;
		}
		$user = new Dase_DBO_DaseUser($db);
		$user->retrieveByEid($eid);
		$tag = new Dase_DBO_Tag($db);
		$tag->ascii_id = $ascii_id;
		$tag->dase_user_id = $user->id;
		$tag->findOne();
		if ($tag->id) {
			return $tag;
		} else {
			return false;
		}
	}

	public function getUrl($app_root)
	{
		$u = $this->getUser();
		return $app_root.'/tag/'.$u->eid.'/'.$this->ascii_id;
	}

	function getCount()
	{
		$tag_item = new Dase_DBO_TagItem($this->db);
		$tag_item->tag_id = $this->id;
		return $tag_item->findCount();
	}

	/** be careful w/ this -- we do not archive before deleting */
	function expunge()
	{
		if (!$this->id) {
			throw new Exception("invalid");
		} 
		$tag_items = new Dase_DBO_TagItem($this->db);
		$tag_items->tag_id = $this->id;

		if ($tag_items->findCount() > 50) {
			throw new Exception("dangerous-looking tag deletion (more than 50 tag items)");
		} 
		foreach ($tag_items->find() as $doomed_tag_item) {
			$doomed_tag_item = clone ($doomed_tag_item);
			$doomed_tag_item->delete();
		}
		$this->delete();
	}

	function getCategories()
	{
		$cat = new Dase_DBO_TagCategory($this->db);
		$cat->tag_id = $this->id;
		return $cat->find();
	}

	function updateItemCount()
	{
		$tag_items = new Dase_DBO_TagItem($this->db);
		$tag_items->tag_id = $this->id;
		$this->item_count = $tag_items->findCount();
		$this->updated = date(DATE_ATOM);
		//postgres boolean weirdness make this necessary
		if (!$this->is_public) {
			$this->is_public = 0;
		}
		$this->update();
	}

	function setBackground($background)
	{
		$this->background = $background;
		if (!$this->is_public) {
			$this->is_public = 0;
		}
		return $this->update();
	}

	function getTagItemIds()
	{
		$prefix = $this->db->table_prefix;
		$dbh = $this->db->getDbh();
		$sql = "
			SELECT id 
			FROM {$prefix}tag_item 
			where tag_id = ?
			ORDER BY sort_order
			";
		$st = $dbh->prepare($sql);
		$st->execute(array($this->id));
		return $st->fetchAll(PDO::FETCH_COLUMN);
	}

	function getTagItems()
	{
		$tag_item = new Dase_DBO_TagItem($this->db);
		$tag_item->tag_id = $this->id;
		$tag_item->orderBy('sort_order');
		return $tag_item->findAll(1);
	}

	public function setItemsStatus($status) 
	{
		foreach ($this->getTagItems() as $ti) {
			$tag_item = clone $ti;
			$tag_item->setItemStatus($status);
		}
	}

	function resortTagItems($dir='DESC')
	{
		$tag_item = new Dase_DBO_TagItem($this->db);
		$tag_item->tag_id = $this->id;
		$tag_item->orderBy('sort_order, updated '.$dir);
		$i = 0;
		foreach ($tag_item->find() as $ti) {
			$i++;
			$ti->sort_order = $i;
			$ti->updated = date(DATE_ATOM);
			$ti->update();
		}
	}

	/** this is for the slideshow sorter */
	function sort($sort_array)
	{
		if (!count($sort_array)) { return; }

		$_moved = array(); 
		$_place_taken = array();
		foreach ($sort_array as $tag_item_id => $new_position) {
			$tag_item = new Dase_DBO_TagItem($this->db);
			$tag_item->load($tag_item_id);
			$tag_item->sort_order = $new_position;
			$tag_item->update();
			$_moved[] = $tag_item->id;
			$_place_taken[] = $new_position;
		}
		$sort_order = 0;
		foreach ($this->getTagItems() as $ti) {
			$ti = clone($ti);
			$sort_order++;
			if (!in_array($ti->id,$_moved)) {
				$sort_order = $this->getNextAvailable($sort_order,$_place_taken);
				$ti->sort_order = $sort_order;
				$ti->update();
			}
		}
	}

	// a bit o' recursion
	function getNextAvailable($sort_order,$_place_taken) {
		if (in_array($sort_order,$_place_taken)) {
			return $this->getNextAvailable($sort_order+1,$_place_taken);
		} else {
			return $sort_order;
		}
	}

	function getType()
	{
		//for compat
		return $this->type;
	}

	function getUser()
	{
		//avoids another db lookup
		if ($this->user) {
			return $this->user;
		}
		$user = new Dase_DBO_DaseUser($this->db);
		$this->user = $user->load($this->dase_user_id);
		return $this->user;
	}

	function addItem($item_unique,$updateCount=false)
	{
		$tag_item = new Dase_DBO_TagItem($this->db);
		$tag_item->tag_id = $this->id;
		list ($coll,$sernum) = explode('/',$item_unique);

		//todo: compat (but handy anyway)
		$item = Dase_DBO_Item::get($this->db,$coll,$sernum);
		if (!$item) { return; }
		$tag_item->item_id = $item->id;

		$tag_item->p_collection_ascii_id = $coll;
		$tag_item->p_serial_number = $sernum;
		$tag_item->updated = date(DATE_ATOM);
		$tag_item->sort_order = 99999;

		try {
			$tag_item->insert();
			//this is too expensive when many items are being added in one request
			if ($updateCount) {
				$this->updateItemCount();
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	function removeItem($item_unique,$update_count=false)
	{
		$tag_item = new Dase_DBO_TagItem($this->db);
		$tag_item->tag_id = $this->id;
		list ($coll,$sernum) = explode('/',$item_unique);

		//todo: compat
		$item = Dase_DBO_Item::get($this->db,$coll,$sernum);
		$tag_item->item_id = $item->id;

		$tag_item->p_collection_ascii_id = $coll;
		$tag_item->p_serial_number = $sernum;
		if ($tag_item->findOne()) {
			$log_text = "removing $item_unique from set $this->eid/$this->ascii_id";
			Dase_Log::info(LOG_FILE,$log_text);
			$tag_item->delete();
			//this is too expensive when many items are being removed in one request
			if ($update_count) {
				$this->updateItemCount();
			}
		}
	}

	function asJson($app_root)
	{

		$collection_lookup = Dase_DBO_Collection::getLookupArray($this->db);
		$json_tag;
		$eid = $this->getUser()->eid;
		$json_tag['id'] = $this->getUrl($app_root);
		$json_tag['uri'] = $this->getUrl($app_root);
		$json_tag['links'] = array('self'=>$this->getUrl($app_root));
		if ($this->created) {
			$json_tag['updated'] = $this->created;
		} else {
			$json_tag['updated'] = date(DATE_ATOM);
		}
		$json_tag['name'] = $this->name;
		$json_tag['description'] = $this->description;
		$json_tag['background'] = $this->background;
		$json_tag['is_public'] = $this->is_public;
		$json_tag['type'] = $this->type;
		$json_tag['eid'] = $eid;
		foreach($this->getTagItems() as $tag_item) {
			$item = $tag_item->getItem();
			if (!$item) {
				Dase_Log::debug(LOG_FILE,'tag_item missing item: '.$tag_item->id);
				continue;
			}
			$json_item = array();
			$json_item['id'] = $app_root.'/tag/'.$eid.'/'.$this->ascii_id.'/'.$tag_item->id; 
			$json_item['links'] = array(); 
			$json_item['links']['self'] = $app_root.'/tag/'.$eid.'/'.$this->ascii_id.'/'.$tag_item->id; 
			$json_item['links']['related'] = $item->getUrl($app_root); 
			$json_item['url'] = $app_root.'/tag/'.$eid.'/'.$this->ascii_id.'/'.$tag_item->id; 
			$json_item['sort_order'] = $tag_item->sort_order;
			//make sure p_ values are always populated!
			$json_item['item_unique'] = $tag_item->p_collection_ascii_id.'/'.$tag_item->p_serial_number;
			$json_item['size'] = $tag_item->size;
			$json_item['updated'] = $tag_item->updated;
			$json_item['annotation'] = $tag_item->annotation;
			$json_item['title'] = $item->getTitle();
			$json_item['collection_name'] = $collection_lookup[$item->collection_id]['collection_name'];

			$json_item['media'] = array();
			foreach ($item->getMedia() as $m) {
				$json_item['media'][$m['size']] = $app_root.$m['url'];
			}

			$json_item['metadata'] = array();
			foreach($item->getMetadata() as $meta){
				$json_item['metadata'][$meta['attribute_name']] = $meta['value_text'];
			}
			$json_tag['items'][] = $json_item;
		}
		return Dase_Json::get($json_tag);
	}

	function asAtom($app_root,$authorized_links=false)
	{
		$this->user || $this->getUser(); 
		$feed = new Dase_Atom_Feed;
		$feed->setTitle($this->name);
		if ($this->description) {
			$feed->setSubtitle($this->description);
		}
		$feed->setId($app_root.'/tag/'. $this->user->eid . '/' . $this->ascii_id);
		$feed->setUpdated($this->updated);
		$feed->addAuthor($this->user->eid);
		$feed->setFeedType('tag');
		$feed->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id.'.atom','self');
		$feed->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id,'alternate');
		$feed->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id.'/list','alternate','text/html','','list');
		$feed->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id.'/grid','alternate','text/html','','grid');
		$feed->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id.'.json','alternate','application/json','','slideshow');

		$feed->addCategory($this->type,"http://daseproject.org/category/tag_type",$this->type);
		if ($this->is_public) {
			$pub = "public";
		} else {
			$pub = "private";
		}
		$feed->addCategory($pub,"http://daseproject.org/category/visibility");
		$feed->addCategory($this->background,"http://daseproject.org/category/background");

		/*  TO DO categories: admin_coll_id, updated, created, master_item, etc */
		$setnum=0;
		$collections_array = array();
		foreach($this->getTagItems() as $tag_item) {

			$tag_item->persist(true);
			$item_unique = $tag_item->p_collection_ascii_id.'/'.$tag_item->p_serial_number;

			//lets us determine if tag includes items in only one collection
			$collections_array[$tag_item->p_collection_ascii_id] = 1;

			if ($authorized_links) {
				//fresh, not from cache
				$item = $tag_item->getItem();
				$entry = $feed->addEntry();
				$entry = $item->injectAtomEntryData($entry,$app_root,true);
			} else {
				$entry = $feed->addItemEntryByItemUnique($this->db,$item_unique,$app_root);
			}

			if ($entry) {
				$setnum++;
				$entry->addCategory($setnum,'http://daseproject.org/category/position');
				$entry->addCategory($tag_item->id,'http://daseproject.org/category/tag_item_id');
				$entry->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id.'/'.$tag_item->id,"http://daseproject.org/relation/search-item");
				$entry->addLink($app_root.'/tag/'.$this->user->eid.'/'.$this->ascii_id.'/'.$tag_item->id.'/annotation',"http://daseproject.org/relation/edit-annotation");
				if ($tag_item->annotation) {
					$entry->setSummary($tag_item->annotation);
				}
			} else {
				//remove tag_item
				$log_text = "SMOKING GUN Ann Johns mystery: tried removing $item_unique from set $this->eid/$this->ascii_id";
				Dase_Log::info(LOG_FILE,$log_text);
				//$tag_item->delete();;
				//$this->resortTagItems();
				//$this->updateItemCount();
			}
		}
		if (1 == count($collections_array)) {
			$coll = array_pop(array_keys($collections_array));
			$feed->addCategory($coll,"http://daseproject.org/category/collection");
		}
		return $feed->asXml();
	}

	function asAtomEntry($app_root,$serialize=true)
	{
		if ($serialize) {
			return $this->injectAtomEntryData(new Dase_Atom_Entry,null,$app_root)->asXml();
		} else {
			return $this->injectAtomEntryData(new Dase_Atom_Entry,null,$app_root);
		}

	}

	function injectAtomEntryData(Dase_Atom_Entry $entry,$user=null,$app_root)
	{
		if (!$user) {
			$user = $this->getUser();
		}
		$entry->setTitle($this->name);
		if ($this->description) {
			$entry->setSummary($this->description);
		}
		$entry->setId($app_root.'/user/'. $user->eid . '/tag/' . $this->ascii_id);
		$updated = $this->updated ? $this->updated : '2005-01-01T00:00:01-06:00';
		$entry->setUpdated($updated);
		$entry->addAuthor($user->eid);
		$entry->addLink($app_root.'/tag/'.$user->eid.'/'.$this->ascii_id.'.atom','self');
		$entry->addLink($app_root.'/tag/'.$user->eid.'/'.$this->ascii_id.'/authorized.atom','http://daseproject.org/relation/authorized');
		$entry->addLink($app_root.'/tag/'.$user->eid.'/'.$this->ascii_id.'/entry.atom','edit' );
		$entry->addLink($app_root.'/tag/'.$user->eid.'/'.$this->ascii_id.'/entry.json','http://daseproject.org/relation/edit','application/json');
		$entry->addLink($app_root.'/tag/'.$user->eid.'/'.$this->ascii_id,'alternate');

		$tag_cat = new Dase_DBO_TagCategory($this->db);
		$tag_cat->tag_id = $this->id;
		foreach ($tag_cat->find() as $tc) {
			$entry->addCategory($tc->term,'http://daseproject.org/category/'.$tc->scheme,$tc->label);
		}

		$entry->addCategory($app_root,"http://daseproject.org/category/base_url");
		$entry->addCategory("set","http://daseproject.org/category/entrytype");
		$entry->addCategory($this->type,"http://daseproject.org/category/tag_type",$this->type);
		if ($this->is_public) {
			$pub = "public";
		} else {
			$pub = "private";
		}
		$entry->addCategory($pub,"http://daseproject.org/category/visibility");
		$entry->addCategory($this->background,"http://daseproject.org/category/background");
		return $entry;
	}

	public function deleteCategories()
	{
		$tag_cat = new Dase_DBO_TagCategory($this->db);
		$tag_cat->tag_id = $this->id;
		foreach ($tag_cat->find() as $tc) {
			$tc->delete();
		}

	}

	public function isBulkEditable($user)
	{
		$prefix = $this->db->table_prefix;
		$dbh = $this->db->getDbh();
		$sql = "
			SELECT p_collection_ascii_id 
			FROM {$prefix}tag_item 
			where tag_id = ?
			GROUP BY p_collection_ascii_id
			";
		$st = $dbh->prepare($sql);
		$st->execute(array($this->id));
		$colls = $st->fetchAll();
		if (1 === count($colls) && $colls[0]['p_collection_ascii_id']) {
			$c = Dase_DBO_Collection::get($this->db,$colls[0]['p_collection_ascii_id']);
			if ($c && $user->can('write',$c)) {
				return true;
			}
		}
		return  false;
	}
}
