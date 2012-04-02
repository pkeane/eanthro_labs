<?php

require_once 'Dase/DBO/Autogen/TagItem.php';

class Dase_DBO_TagItem extends Dase_DBO_Autogen_TagItem 
{
	function getItem()
	{
		$item = new Dase_DBO_Item($this->db);
		if ($item->load($this->item_id)) {
			$item->getCollection();
			return $item;
		} else {
			if ($this->p_collection_ascii_id && $this->p_serial_number) {
				$item = Dase_DBO_Item::get($this->db,$this->p_collection_ascii_id,$this->p_serial_number); 
				if ($item) {
					$item->getCollection();
					return $item;
				} 
			}
		}
		return false;
	}

	public function setItemStatus($status) 
	{
		if (in_array($status,array('public','draft','delete','archive'))) {
			$item = $this->getItem();
			if ($item) {
				$item->status = $status;
				$item->update();
			}
		}
	}

	function getTag()
	{
		$tag = new Dase_DBO_Tag($this->db);
		if ($tag->load($this->tag_id)) {
			return $tag;
		} else {
			return false;
		}
	}

	function persist($no_auto_db_check=false)
   	{
		if ($no_auto_db_check && $this->p_collection_ascii_id && $this->p_serial_number) {
			return;
		}
		$prefix = $this->db->table_prefix;
		$dbh = $this->db->getDbh();
		$sql = "
			SELECT c.ascii_id as collection_ascii_id,i.serial_number
			FROM {$prefix}tag_item t, {$prefix}collection c, {$prefix}item i
			WHERE i.id = t.item_id
			AND i.collection_id = c.id
			AND t.id = ? 
			";
		$sth = $dbh->prepare($sql);
		$sth->execute(array($this->id));
		$row = $sth->fetch();
		$this->p_collection_ascii_id = $row['collection_ascii_id'];
		$this->p_serial_number = $row['serial_number'];
		$this->update();
		return $this;
	}

	function asAtom($app_root)
	{
		$item = $this->getItem();
		$tag = $this->getTag();
		$feed = new Dase_Atom_Feed;
		if (is_numeric($item->updated)) {
			$updated = date(DATE_ATOM,$item->updated);
		} else {
			$updated = $item->updated;
		}
		$feed->setUpdated($updated);
		$feed->setTitle($item->getTitle());
		$feed->setId($app_root.'/tag/item/'.$tag->id.'/'.$this->id);
		$feed->setGenerator('DASe','http://daseproject.org','1.0');
		$feed->addAuthor($tag->eid);

		//$feed->addCategory($tag->type,"http://daseproject.org/category/tag_type",$tag->type);
		$feed->addCategory('set',"http://daseproject.org/category/tag_type");
		//$feed->addLink($tag->getUrl($app_root),"http://daseproject.org/relation/feed-link");
		$feed->addLink($tag->getUrl($app_root),"up");
		$tag_item_id_array = $tag->getTagItemIds();
		$position = array_search($this->id,$tag_item_id_array) + 1;
		$feed->addCategory($position,"http://daseproject.org/category/position");

		if (1 == $position) {
			$prev_id = array_pop($tag_item_id_array);
			array_push($tag_item_id_array,$prev_id); //because array_pop shortened array
		} else {
			$prev_id = $tag_item_id_array[$position-2];
		}

		if (isset($tag_item_id_array[$position])) {
			$next_id = $tag_item_id_array[$position];
		} else {
			$next_id = $tag_item_id_array[0];
		}
		//overloading opensearch elements here 
		$feed->setOpensearchTotalResults($tag->item_count);
		$feed->setOpensearchQuery($tag->name);


		//$feed->addLink($tag->getLink().'/'.$prev_id,"previous");
		//$feed->addLink($tag->getLink().'/'.$next_id,"next");
		$feed->addLink($app_root.'/tag/item/'.$tag->id.'/'.$this->id.'.atom',"self");
		$feed->addLink($app_root.'/tag/item/'.$tag->id.'/'.$prev_id,"previous");
		$feed->addLink($app_root.'/tag/item/'.$tag->id.'/'.$next_id,"next");
		$feed->setFeedType('tagitem');
		//tag name goes in subtitle, so doesn't need to be in category
		$feed->setSubtitle($tag->name.' '.$position.' of '.count($tag_item_id_array));
		//regenerated!!! (should cache)
		$entry = $item->injectAtomEntryData($feed->addEntry(),$app_root);
		//very strange to use summary for annotation (?)
		$entry->setSummary($this->annotation);
		return $feed->asXml();
	}
}
