<?php

require_once 'Dase/DBO/Autogen/CollectionManager.php';

class Dase_DBO_CollectionManager extends Dase_DBO_Autogen_CollectionManager 
{
	public $name;

	public function getUser()
	{
		$user = new Dase_DBO_DaseUser($this->db);
		$user->eid = $this->dase_user_eid;
		return	$user->findOne();
	}

	public static function get($db,$coll_ascii_id,$eid)
	{
		$cm = new Dase_DBO_CollectionManager($db);
		$cm->collection_ascii_id = $coll_ascii_id;
		$cm->dase_user_eid = $eid;
		if ($cm->findOne()) {
			return $cm;
		} else {
			return false;
		}
	}

	public static function listAsAtom($db,$app_root)
	{
		$cm = new Dase_DBO_CollectionManager($db);
		$cms = $cm->find();
		$feed = new Dase_Atom_Feed;
		$feed->setTitle('DASe Collection Managers');
		$feed->setId($app_root.'/admin/managers');
		//fix to be latest update
		$feed->setUpdated(date(DATE_ATOM));
		$feed->addAuthor();
		$feed->addLink($app_root.'/admin/managers.atom','self');
		$feed->addCategory($app_root,"http://daseproject.org/category/base_url");
		foreach ($cms as $manager) {
			$entry = $feed->addEntry();
			$manager->injectAtomEntryData($entry,$app_root);
		}
		return $feed->asXml();
	}

	function asAtom($app_root) 
	{
		$e = new Dase_Atom_Entry();
		return $this->injectAtomEntryData($e,$app_root);

	}

	function injectAtomEntryData(Dase_Atom_Entry $entry,$app_root)
	{
		$entry->setTitle('Collection Manager '.$this->collection_ascii_id.'/'.$this->dase_user_eid);
		$entry->setId($app_root.'/manager/'.$this->collection_ascii_id.'/'.$this->dase_user_eid);
		$entry->addCategory('collection_manager','http://daseproject.org/category/entrytype');
		$entry->setUpdated($this->created);
		$entry->addAuthor($this->created_by_eid);
		$entry->addCategory($this->auth_level,'http://daseproject.org/category/auth_level');
		$entry->addCategory($this->dase_user_eid,'http://daseproject.org/category/eid');
		$entry->addCategory($this->collection_ascii_id,'http://daseproject.org/category/collection');
		return $entry;
	}
}
