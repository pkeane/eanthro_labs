<?php

require_once 'Dase/DBO/Autogen/ItemType.php';

class Dase_DBO_ItemType extends Dase_DBO_Autogen_ItemType 
{
	public $attributes;
	public $collection;

	public static function get($db,$collection_ascii_id,$ascii_id)
	{
		if ($collection_ascii_id && $ascii_id) {
			$item_type = new Dase_DBO_ItemType($db);
			$item_type->ascii_id = $ascii_id;
			$item_type->collection_id = Dase_DBO_Collection::get($db,$collection_ascii_id)->id;
			return($item_type->findOne());
		} else {
			throw new Exception('missing a method parameter value');
		}
	}

	public static function findOrCreate($db,$collection_ascii_id,$ascii_id) 
	{
		$type = new Dase_DBO_ItemType($db);
		$coll = Dase_DBO_Collection::get($db,$collection_ascii_id);
		if (!$coll) {
			throw new Exception('no such collection');
		}
		$type->collection_id = $coll->id;
		$type->ascii_id = Dase_Util::dirify($ascii_id);
		if (!$type->findOne()) {
			$type->name = ucwords(str_replace('_',' ',$ascii_id));
			$type->insert();
		}
		return $type;
	}

	public function getUrl($collection_ascii_id,$app_root)
	{
		return $app_root.'/item_type/'.$collection_ascii_id.'/'.$this->ascii_id;
	}

	public function asAtomEntry($collection_ascii_id,$app_root)
	{
		$c = $this->getCollection();
		$entry = new Dase_Atom_Entry_ItemType;
		$entry = $this->injectAtomEntryData($entry,$collection_ascii_id,$app_root);
		return $entry->asXml();
	}

	function injectAtomEntryData(Dase_Atom_Entry $entry,$collection_ascii_id,$app_root)
	{
		$entry->setTitle($this->name);
		$entry->setId($this->getUrl($collection_ascii_id,$app_root));
		$entry->setSummary($this->description);
		$entry->addLink($this->getUrl($collection_ascii_id,$app_root).'.atom','edit');
		$entry->addLink($this->getUrl($collection_ascii_id,$app_root).'/attributes.atom','http://daseproject.org/relation/item_type/attributes','application/atom+xml','',$this->name.' Attributes');
		$entry->addLink($this->getUrl($collection_ascii_id,$app_root).'/attributes.json','http://daseproject.org/relation/item_type/attributes','application/json','',$this->name.' Attributes');
		$entry->addCategory('item_type','http://daseproject.org/category/entrytype','Item Type');
		if (is_numeric($this->updated)) {
			$updated = date(DATE_ATOM,$this->updated);
		} else {
			$updated = $this->updated;
		}
		$entry->setUpdated($updated);
		$entry->addAuthor();
		return $entry;
	}

	public function getCollection() {

		if ($this->collection) {
			return $this->collection;
		}
		$c = new Dase_DBO_Collection($this->db);
		$c->load($this->collection_id);
		$this->collection = $c;
		return $c;
	}

	public function getAtts() {
		//for lazy load from smarty (since there is an 'attributes' member
		return $this->getAttributes();
	}

	public function getAttributes()
	{
		$attributes = array();
		if ('default' == $this->ascii_id) {
			$c = $this->getCollection();
			foreach ($c->getAttributes() as $att) {
				$att = clone($att);
				$att->getFormValues();
				$attributes[strtolower($att->attribute_name)] = $att;
			}
		} else {
			$att_it = new Dase_DBO_AttributeItemType($this->db);
			$att_it->item_type_id = $this->id;
			foreach($att_it->find() as $ait) {
				$att = new Dase_DBO_Attribute($this->db);
				if ($att->load($ait->attribute_id)) {
					$att->getFormValues();
					//	for sorting
					//	are attribute names unique???
					$attributes[strtolower($att->attribute_name)] = $att;
				}
			}
		}
		ksort($attributes);
		$this->attributes = $attributes;
		return $attributes;
	}

	public function getItemsCount() {
		$i = new Dase_DBO_Item($this->db);
		$i->item_type_id = $this->id;
		return $i->findCount();
	}

	public function getAttributesFeed($collection_ascii_id,$app_root) 
	{
		$feed = new Dase_Atom_Feed;
		$feed->setTitle($this->name.' Attributes');
		$feed->setId($app_root.'/item_type/'. $collection_ascii_id . '/' . $this->ascii_id.'/attributes');
		$feed->setUpdated(date(DATE_ATOM));
		foreach($this->getAttributes() as $att) {
			$entry = $feed->addEntry('attribute');
			$att->injectAtomEntryData($entry,$collection_ascii_id,$app_root);
		}
		return $feed->asXml();
	}

	public function getAttributesJson($collection_ascii_id,$app_root) 
	{
		$atts = array();
		foreach ($this->getAttributes() as $att) {
			$a = $att->asArray();
			$a['att_ascii_id'] = $att->ascii_id;
			$a['item_type_ascii'] = $this->ascii_id;
			//$a['attribute_name'] = $att->attribute_name;
			$a['href'] = $att->getUrl($collection_ascii_id,$app_root);
			$atts[] = $a;
		}
		return Dase_Json::get($atts);
	}

	public function expunge()
	{
		if (!$this->id || !$this->ascii_id) {
			throw new Exception('cannot delete unspecified type');
		}
		$ait = new Dase_DBO_AttributeItemType($this->db);
		$ait->item_type_id = $this->id;
		foreach ($ait->find() as $doomed) {
			Dase_Log::info(LOG_FILE,'deleted attribute_item_type '.$doomed->id);
			$doomed->delete();
		}
		$this->delete();
	}
}
