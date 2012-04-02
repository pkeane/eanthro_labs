<?php

class Dase_Handler_Tag extends Dase_Handler
{

	public $resource_map = array( 
		'{tag_id}' => 'tag',
		'{eid}/{tag_ascii_id}' => 'tag',
		'{eid}/{tag_ascii_id}/authorized' => 'authorized',
		'{eid}/{tag_ascii_id}/download' => 'tag_zip_archive',
		'{eid}/{tag_ascii_id}/entry' => 'tag_entry', 
		'{eid}/{tag_ascii_id}/background' => 'background',
		'{eid}/{tag_ascii_id}/visibility' => 'visibility',
		'{eid}/{tag_ascii_id}/metadata' => 'metadata',
		'{eid}/{tag_ascii_id}/item_status' => 'item_status',
		'{eid}/{tag_ascii_id}/list' => 'tag_list',
		'{eid}/{tag_ascii_id}/grid' => 'tag_grid',
		'{eid}/{tag_ascii_id}/annotate' => 'tag_annotate',
		'{eid}/{tag_ascii_id}/item_uniques' => 'item_uniques',
		'{eid}/{tag_ascii_id}/common_keyvals' => 'common_keyvals',
		'{eid}/{tag_ascii_id}/sorter' => 'tag_sorter',
		'{eid}/{tag_ascii_id}/expunger' => 'tag_expunger',
		//for set delete:
		'{eid}/{tag_ascii_id}/items' => 'tag_items',
		'item/{tag_id}/{tag_item_id}' => 'tag_item',
		'{eid}/{tag_ascii_id}/{tag_item_id}' => 'tag_item',
		'item/{tag_id}/{tag_item_id}/annotation' => 'annotation',
		'{eid}/{tag_ascii_id}/{tag_item_id}/annotation' => 'annotation',
		'{eid}/{tag_ascii_id}/item/{collection_ascii_id}/{serial_number}' => 'tag_item',
	);

	protected function setup($r)
	{
		//Locates requested tag.  Method still needs to authorize.
		$tag = new Dase_DBO_Tag($this->db);
		if ($r->has('tag_ascii_id') && $r->has('eid')) {
			$tag->ascii_id = $r->get('tag_ascii_id');
			$tag->eid = $r->get('eid');
			$found = $tag->findOne();
		} elseif ($r->has('tag_id')) {
			$found = $tag->load($r->get('tag_id'));
		} 
		if (isset($found) && $found && $found->id) {
			$this->tag = $tag;
		} else {
			$r->renderError(404,'no such tag');
		}
	}	

	public function getTagZipArchive($r) 
	{
		$out = '';
		$tag = $this->tag;
		$u = $r->getUser();
		if (!$u->can('write',$tag)) {
			$r->renderError(401,'user does not have download privileges');
		}

		if ($tag->item_count > 30) {
			$params['msg'] = 'Sorry, only sets of 30 or fewer items may be downloaded.  Please split into multiple sets.';
			$r->renderRedirect('tag/'.$u->eid.'/'.$this->tag->ascii_id,$params);
		}
		//ZIP stuff
		$zip = new ZipArchive();
		$filename = MEDIA_DIR."/tmp/".$u->eid."-".$tag->ascii_id.".zip";
		if (file_exists($filename)) {
			unlink($filename);
		}
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
			$r->renderError(401,'cannot create zip');
		}
		foreach ($tag->getTagItems() as $ti) {
			$item = $ti->getItem();
			//todo work on other sizes
			foreach (array('small','medium','large','full') as $size) {
			//foreach (array('small','medium','large') as $size) {
				/*
				$img = file_get_contents($item->getMediaUrl($size,$r->app_root));
				if (strlen($img)) {
					$zip->addFromString($u->eid.'-'.$tag->ascii_id.'/'.$size.'/'.$item->serial_number.'.jpg',$img);
				}
				 */
				$fn = MEDIA_DIR."/tmp/".$u->eid.'-'.$tag->ascii_id.'-'.$size.'-'.$item->serial_number;
				file_put_contents($fn,file_get_contents($item->getMediaUrl($size,$r->app_root,$this->config->getAuth('token'))));
				if (filesize($fn)) {
					$zip->addFile($fn,$u->eid.'-'.$tag->ascii_id.'/'.$size.'/'.$item->serial_number.'.jpg');
				}

			}
		}
		$zip->close();
		//todo: need to set a cron job to garbage collect the set in media/tmp
		$r->serveFile($filename,'application/zip',true);
	}

	public function getTagAtom($r)
	{
		$u = $r->getUser('http');
		if (!$u->can('read',$this->tag)) {
			$r->renderError(401,'user '.$u->eid.' is not authorized to read tag');
		}
		if (!$r->get('nocache')) {
			$r->checkCache();
		}
		if ('entry' == $r->get('type')) {
			$r->renderResponse($this->tag->asAtomEntry($r->app_root));
		} else {
			$r->renderResponse($this->tag->asAtom($r->app_root));
		}
	}

	public function getCommonKeyvalsJson($r) 
	{
		$set = array();
		$common = array();
		foreach ($this->tag->getTagItems() as $ti) {
			$item = $ti->getItem();
			if ($item) {
				if (!isset($set[$item->serial_number])) {
					$set[$item->serial_number] = array();
				}
				$meta = $item->getMetadata();
				foreach ($meta as $m) {
					$set[$item->serial_number][$m['attribute_name'].' : '.$m['value_text']] = array( $m['ascii_id'],$m['value_text'] );
				}
				$last = $set[$item->serial_number];
			}
		}
		//foreach keyval in the last item
		foreach ($last as $ascii_plus_val => $keyval) {
			$common[$ascii_plus_val] = $keyval;

			//iterate through all items checking for same keyval
			foreach ($set as $sernum => $metas) {
				if (!isset($metas[$ascii_plus_val])) {
					unset($common[$ascii_plus_val]);
				}
			}
		}
		$r->renderResponse(Dase_Json::get($common));
	}

	public function getAuthorizedAtom($r)
	{
		$u = $r->getUser('http');
		if (!$u->can('read',$this->tag)) {
			$r->renderError(401,'user '.$u->eid.' is not authorized to read tag');
		}
		if (!$r->get('nocache')) {
			$r->checkCache();
		}
		if ('entry' == $r->get('type')) {
			$r->renderResponse($this->tag->asAtomEntry($r->app_root));
		} else {
			$r->renderResponse($this->tag->asAtom($r->app_root,true));
		}
	}

	public function getTagEntryJson($r)
	{
		$r->renderResponse($this->tag->asAtomEntry($r->app_root,false)->asJson($r->app_root));
	}

	public function getTagEntryAtom($r)
	{
		$r->renderResponse($this->tag->asAtomEntry($r->app_root));
	}

	public function getTagJson($r)
	{
		$u = $r->getUser();
		if (!$u->can('read',$this->tag)) {
			$r->renderError(401);
		}
		$r->renderResponse($this->tag->asJson($r->app_root));
	}

	public function getTagList($r)
	{
		$this->getTag($r,'list');
	}

	public function getTagGrid($r)
	{
		$this->getTag($r,'grid');
	}

	public function getTagAnnotate($r)
	{
		$u = $r->getUser();
		if (!$u->can('write',$this->tag)) {
			$r->renderError(401,$u->eid .' is not authorized to write this resource');
		}
		$r->checkCache();
		$t = new Dase_Template($r);
		//cannot use eid/ascii since it'll sometimes be another user's tag
		$json_url = $r->app_root.'/tag/'.$this->tag->id.'.json';
		$t->assign('json_url',$json_url);
		$feed_url = $r->app_root.'/tag/'.$this->tag->id.'.atom';
		$t->assign('items',Dase_Atom_Feed::retrieve($feed_url,$u->eid,$u->getHttpPassword()));
		$r->renderResponse($t->fetch('item_set/annotate.tpl'));
	}

	public function getTag($r,$display='')
	{
		$u = $r->getUser();
		if (!$u->can('read',$this->tag)) {
			$r->renderError(401,$u->eid .' is not authorized to read this resource.');
		}
		if (!$r->get('nocache')) {
			$r->checkCache();
		}
		$t = new Dase_Template($r);
		//cannot use eid/ascii since it'll sometimes be another user's tag
		$json_url = $r->app_root.'/tag/'.$this->tag->id.'.json';
		$t->assign('json_url',$json_url);
		$feed_url = $r->app_root.'/tag/'.$this->tag->id.'.atom';
		if ($r->get('nocache')) {
			$feed_url .= '?nocache=1';
		}
		$t->assign('feed_url',$feed_url);
		$t->assign('items',Dase_Atom_Feed::retrieve($feed_url,$u->eid,$u->getHttpPassword()));
		if ($u->can('admin',$this->tag) && 'hide' != $u->controls_status) {
			$t->assign('bulkedit',1);
		}
		if ($this->tag->is_public) {
			$t->assign('is_public',1);
		}
		if ($u->can('write',$this->tag)) {
			$t->assign('is_admin',1);
		}
		//grid is default, data has it's own method
		if (!$display && 'list' == $u->display) {
			$display = 'list';
		}	
		$t->assign('display',$display);
		$r->renderResponse($t->fetch('item_set/tag.tpl'));
	}

	public function getTagSorter($r)
	{
		$u = $r->getUser();
		if (!$u->can('read',$this->tag)) {
			$r->renderError(401,$u->eid .' is not authorized to read this resource');
		}
		$t = new Dase_Template($r);
		//always get fresh (no cache)
		$feed_url = $r->app_root.'/tag/'.$this->tag->id.'.atom?nocache=1';
		$t->assign('tag_feed',Dase_Atom_Feed::retrieve($feed_url,$u->eid,$u->getHttpPassword()));
		$r->renderResponse($t->fetch('item_set/tag_sorter.tpl'));
	}

	public function postToTagSorter($r)
	{
		$u = $r->getUser();
		if (!$u->can('write',$this->tag)) {
			$r->renderError(401,$u->eid .' is not authorized to write this resource');
		}
		$sort_array = $r->get('set_sort_item',true);
		$this->tag->sort($sort_array);
		$r->renderRedirect('tag/'.$u->eid.'/'.$this->tag->ascii_id.'/sorter');
	}

	public function getTagItemAtom($r)
	{
		$tag_item = new Dase_DBO_TagItem($this->db);
		if (!$tag_item->load($r->get('tag_item_id'))) {
			$r->renderAtomError(404);
		}
		if (!$tag_item->getItem()) {
			$r->renderAtomError(404);
		} 
		if ($tag_item->tag_id != $this->tag->id) {
			$r->renderAtomError(404);
		} 
		$r->renderResponse($tag_item->asAtom($r->app_root));
	}

	public function getTagItem($r)
	{
		$u = $r->getUser();
		$tag_ascii_id = $r->get('tag_ascii_id');
		$tag_item_id = $r->get('tag_item_id');
		$t = new Dase_Template($r);
		//$t->assign('item',Dase_Atom_Feed::retrieve($r->app_root.'/tag/'.$u->eid.'/'.$tag_ascii_id.'/'.$tag_item_id.'?format=atom',$u->eid,$u->getHttpPassword()));
		$t->assign('item',Dase_Atom_Feed::retrieve($r->app_root.'/tag/item/'.$this->tag->id.'/'.$tag_item_id.'?format=atom',$u->eid,$u->getHttpPassword()));
		$r->renderResponse($t->fetch('item/display.tpl'));
	}

	public function postToMetadata($r)
	{
		$user = $r->getUser();
		if (!$user->can('admin',$this->tag)) {
			$r->renderError(401,'cannot post tag metadata');
		}
		$att_ascii = $r->get('ascii_id');
		foreach ($this->tag->getTagItems() as $tag_item) {
			$item = $tag_item->getItem();
			if ($item) {
				foreach ($r->get('value',true) as $val) {
					$item->setValue($att_ascii,$val);
				}
				//do not commit
				$item->buildSearchIndex(false);
			}
		}
		$solr = new Dase_Solr($this->db,$this->config);
		$solr->commit();
		$r->renderRedirect('tag/'.$user->eid.'/'.$this->tag->ascii_id.'/list');
	}

	public function postToBackground($r)
	{
		$user = $r->getUser();
		if (!$user->can('write',$this->tag)) {
			$r->renderError(401,'not authorized to set background');
		}
		$this->tag->setBackground($r->get('background'));
		$r->renderRedirect('tag/'.$user->eid.'/'.$this->tag->ascii_id.'/annotate');
	}

	public function postToVisibility($r)
	{
		$user = $r->getUser();
		if (!$user->can('write',$this->tag)) {
			$r->renderError(401,'not authorized to set visibility');
		}
		if ('public' == $r->get('visibility')) {
			$this->tag->is_public = 1;
		}
		if ('private' == $r->get('visibility')) {
			$this->tag->is_public = 0;
		}
		$this->tag->update();
		if ('list' == $r->get('display')) {
			$r->renderRedirect('tag/'.$user->eid.'/'.$this->tag->ascii_id.'/list?nocache=1');
		} else {
			$r->renderRedirect('tag/'.$user->eid.'/'.$this->tag->ascii_id.'?nocache=1');
		}
	}

	/** bulk item status set */
	public function postToItemStatus($r)
	{
		$user = $r->getUser();
		if (!$user->can('write',$this->tag)) {
			$r->renderError(401);
		}
		$this->tag->setItemsStatus($r->get('status'));
		if ('list' == $r->get('display')) {
			$r->renderRedirect('tag/'.$user->eid.'/'.$this->tag->ascii_id.'/list?nocache=1');
		} else {
			$r->renderRedirect('tag/'.$user->eid.'/'.$this->tag->ascii_id.'?nocache=1');
		}
	}

	public function putAnnotation($r) 
	{
		$u = $r->getUser();
		$tag = $this->tag;
		if (!$u->can('write',$tag)) {
			$r->renderError(401);
		}
		$tag_item = new Dase_DBO_TagItem($this->db);
		$tag_item->load($r->get('tag_item_id'));
		$tag_item->annotation = Dase_Util::stripInvalidXmlChars($r->getBody());
		$tag_item->updated = date(DATE_ATOM);
		$tag_item->update();
		$r->renderResponse($tag_item->annotation);
	}

	public function postToTag($r) 
	{
		//this should be reworked to get text/uri-list
		//OR an atom entry that lists tag_items a la OAI-ORE
		$tag = $this->tag;
		$u = $r->getUser();
		$u->expireDataCache($r->getCache());
		if (!$u->can('write',$tag)) {
			$r->renderError(401);
		}
		$item_uniques_array = explode(',',$r->getBody());
		$num = count($item_uniques_array);
		foreach ($item_uniques_array as $item_unique) {
			$tag->addItem($item_unique);
		}
		//also sets 'updated':
		$this->tag->updateItemCount();
		$this->tag->resortTagItems();
		$r->response_mime_type = 'text/plain';
		$r->renderResponse("added $num items to $tag->name");
	}

	public function postToTagExpunger($r) 
	{
		$tag = $this->tag;
		$u = $r->getUser();
		$u->expireDataCache($r->getCache());
		if (!$u->can('write',$tag)) {
			$r->renderError(401);
		}
		try {
			$tag->expunge();
		} catch (Exception $e) {
			$r->renderError(400,$e->getMessage());
		}
		$params['msg'] = 'successfully deleted set';
		$r->renderRedirect("collections",$params);
	}

	public function deleteTagItems($r) 
	{
		//move some of this into model
		$tag = $this->tag;
		$u = $r->getUser();
		Dase_Log::info(LOG_FILE,"$u->eid ($u->name) is fixing to delete a bunch of items from $tag->ascii_id");
		$u->expireDataCache($r->getCache());
		if (!$u->can('write',$tag)) {
			$r->renderError(401,'user does not have write privileges');
		}
		$item_uniques_array = explode(',',$r->get('uniques'));
		$num = count($item_uniques_array);
		foreach ($item_uniques_array as $item_unique) {
			$tag->removeItem($item_unique);
		}
		$tag->resortTagItems();
		$tag->updateItemCount();
		$r->response_mime_type = 'text/plain';
		$r->renderResponse("removed $num items from $tag->name");
	}

	public function putTag($r)
	{
		$user = $r->getUser('http');
		if (!$user->can('write',$this->tag)) {
			$r->renderError(401,'cannot update set');
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
				$set_entry = Dase_Atom_Entry::load($raw_input);
			} catch(Exception $e) {
				Dase_Log::debug(LOG_FILE,'tag handler error: '.$e->getMessage());
				$r->renderError(400,'bad xml');
			}
			if ('set' != $set_entry->entrytype) {
				$r->renderError(400,'must be a set entry');
			}
			$set = $set_entry->update($this->db,$r);
			if ($set) {
				$r->renderOk('set updated');
			} else {
				$r->renderError(500);
			}
		}
		$r->renderError(500);
	}

	public function putTagEntry($r) 
	{
		$this->putTag($r);
	}

}

