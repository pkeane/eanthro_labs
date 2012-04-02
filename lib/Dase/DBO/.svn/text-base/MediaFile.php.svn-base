<?php

require_once 'Dase/DBO/Autogen/MediaFile.php';

class Dase_DBO_MediaFile extends Dase_DBO_Autogen_MediaFile 
{
	public $url = '';

	function getItem()
	{
		$item = new Dase_DBO_Item($this->db);
		$item->load($this->item_id);
		return $item;
	}

	function getCollection()
	{
		$coll = new Dase_DBO_Collection($this->db);
		$coll->load($this->getItem()->collection_id);
		return $coll;
	}

	public function moveFileToDeleted($path_to_media) {
		$c = $this->getCollection();
		$dest = $path_to_media.'/'.$c->ascii_id.'/deleted/'.$this->filename; 
		$src = $this->getLocalPath($path_to_media);

		//don't delete if it is a media icon
		$icon_path = BASE_PATH.'/www/images/thumb_icons/'.$this->filename;
		if (file_exists($icon_path)) { return; }

		if (file_exists($src) && copy($src,$dest)) {
			@unlink($src);
		}
	}

	public function getLocalPath($path_to_media)
	{
		$c = $this->getCollection();
		$subdir = Dase_Util::getSubdir($this->p_serial_number);
		$path = $path_to_media.'/'.
			$c->ascii_id.'/'.
			$this->size.'/'.
			$subdir.'/'.
			$this->filename;
		if (file_exists($path)) {
			return $path;
		} else {
			$path = $path_to_media.'/'.
				$c->ascii_id.'/'.
				$this->size.'/'.
				$this->filename;
			return $path;
		}
	}

	function getLink($app_root) {
		return "$app_root/media/{$this->p_collection_ascii_id}/$this->size/$this->filename";
	}

	function getRelativeLink() {
		return "media/{$this->p_collection_ascii_id}/$this->size/$this->filename";
	}

	public static function getUniqueBaseIdent($db,$title,$collection_ascii_id)
	{
		$check_ident = Dase_Util::dirify($title);
		$mf = new Dase_DBO_MediaFile($db);
		$mf->p_serial_number = $check_ident;
		$mf->p_collection_ascii_id = $collection_ascii_id;
		if (!$mf->findOne()) {
			return $check_ident;
		} else {
			$check_ident = $check_ident.time();
			return Dase_DBO_MediaFile::getUniqueBaseIdent($db,$check_ident,$collection_ascii_id);
		}
	}

	function asAtom($app_root) 
	{
		$entry = new Dase_Atom_Entry;
		//may need to add edit links here
		return $this->injectAtomEntryData($entry,$app_root);
	}

	function getDerivatives()
	{
		$m = new Dase_DBO_MediaFile($this->db);
		$m->p_collection_ascii_id = $this->p_collection_ascii_id;
		$m->p_serial_number = $this->p_serial_number;
		$m->orderBy('width');
		$m->addWhere('size',$this->size,'!=');
		return $m->find();
	}

	function injectAtomEntryData(Dase_Atom_Entry $entry,$app_root)
	{
		$d = "http://daseproject.org/ns/1.0";
		//this function assumes p_collection_ascii_id & p_serial_number are set
		$entry->setId($this->getLink($app_root));
		$entry->setTitle($this->filename);
		$entry->addAuthor();

		$entry->setUpdated($this->updated);
		$entry->setSummary('');

		//for AtomPub
		$entry->setEdited($this->updated);
		$edit_url = $app_root.'/media/'.$this->p_collection_ascii_id.'/'.$this->p_serial_number;
		$entry->addLink($edit_url,'edit');
		$ext = Dase_File::$types_map[$this->mime_type]['ext'];
		$edit_media_url = $app_root.'/media/'.$this->p_collection_ascii_id.'/'.$this->p_serial_number;
		$entry->addLink($edit_media_url,'edit-media');

		//to which we can post form-encoded or json metadata pairs:
		$entry->addLink(
			$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->p_serial_number.'/metadata',
			'http://daseproject.org/relation/edit-metadata');

		//link to the owning dase item using iana registered 'up' rel value
		$entry->addLink(
			$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->p_serial_number.'.atom',
			'up');

		$entry->setMediaContent($this->getLink($app_root),$this->mime_type);
		$media_group = $entry->addElement('media:group',null,Dase_Atom::$ns['media']);
		//todo: beef up w/ bitrate, samplingrate, etc.
		foreach ($this->getDerivatives() as $med) {
			if ($med->size == 'thumbnail') {
				//$media_thumbnail = $entry->addElement('media:thumbnail',null,Dase_Atom::$ns['media']);
				$media_thumbnail = $media_group->appendChild($entry->dom->createElementNS(Dase_Atom::$ns['media'],'thumbnail'));
				$media_thumbnail->setAttribute('url',$med->getLink($app_root));
				$media_thumbnail->setAttribute('width',$med->width);
				$media_thumbnail->setAttribute('height',$med->height);
			}
		   	if ($med->size == 'viewitem') {
				//$media_viewitem = $entry->addElement('media:content',null,Dase_Atom::$ns['media']);
				$media_viewitem = $media_group->appendChild($entry->dom->createElementNS(Dase_Atom::$ns['media'],'content'));
				$media_viewitem->setAttribute('url',$med->getLink($app_root));
				$media_viewitem->setAttribute('width',$med->width);
				$media_viewitem->setAttribute('height',$med->height);
				$media_viewitem->setAttribute('fileSize',$med->file_size);
				$media_viewitem->setAttribute('type',$med->mime_type);
				$media_category = $media_viewitem->appendChild($entry->dom->createElement('media:category'));
				$media_category->appendChild($entry->dom->createTextNode($med->size));
			}
			if ($med->size != 'thumbnail' && $med->size != 'viewitem') {
				$media_content = $media_group->appendChild($entry->dom->createElementNS(Dase_Atom::$ns['media'],'content'));
				$media_content->setAttribute('url',$med->getLink($app_root));
				$media_content->setAttribute('width',$med->width);
				$media_content->setAttribute('height',$med->height);
				$media_content->setAttribute('fileSize',$med->file_size);
				$media_content->setAttribute('type',$med->mime_type);
				$media_category = $media_content->appendChild($entry->dom->createElement('media:category'));
				$media_category->appendChild($entry->dom->createTextNode($med->size));
			}
		}
		return $entry->asXml();
	}
}
