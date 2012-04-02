<?php

require_once 'Dase/DBO/Autogen/Comment.php';

class Dase_DBO_Comment extends Dase_DBO_Autogen_Comment 
{

	public $item = null;

	function getItem()
	{
		if (!$this->item) {
			$this->item = Dase_DBO_Item::get($this->db,$this->p_collection_ascii_id,$this->p_serial_number);
		}
		return $this->item;
	}

	function getTitle()
	{
		list($first_line) = explode("\n",$this->text);
		if (strlen($first_line) > 100) {
			$first_line = substr($first_line,0,100);
		}
		return $first_line;
	}

	function getUrl($app_root)
	{
		return $app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->p_serial_number.'/comments/'.$this->id;
	}

	function injectAtomEntryData(Dase_Atom_Entry_Comment $entry,$app_root)
	{
		if (!$this->id) { return false; }
		$item = $this->getItem();
		$entry->setTitle($this->getTitle());
		$entry->addAuthor($this->updated_by_eid);
		//for AtomPub -- is this correct??
		$entry->addLink($this->getUrl($app_root));
		$entry->addLink($this->getUrl($app_root).'.atom','self','application/atom+xml');
		$entry->addLink($this->getUrl($app_root).'.atom','edit','application/atom+xml');
		$entry->setUpdated($this->updated);
		$entry->setId($this->getUrl($app_root));
		if (!$this->type) { $this->type = 'text/html'; }
		$entry->setContent($this->text,$this->type);
		//add in-reply-to link
		$item_url = $item->getUrl($app_root);
		$entry->addInReplyTo($item_url,$this->type,$item_url);
		return $entry;
	}

}
