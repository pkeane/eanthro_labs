<?php

require_once 'Dase/DBO/Autogen/Item.php';

class Dase_DBO_Item extends Dase_DBO_Autogen_Item 
{

		private $_collection = null;
		private $_content = null;
		private $_item_type = null;
		private $_media = array();
		private $_metadata = array();

		public static function get($db,$collection_ascii_id,$serial_number)
		{
				if (!$collection_ascii_id || !$serial_number) {
						throw new Exception('missing information');
				}
				$item = new Dase_DBO_Item($db);
				$item->p_collection_ascii_id = $collection_ascii_id;
				$item->serial_number = $serial_number;
				if ($item->findOne()) {
						return $item;
				} else {
						return false;
				}
		}

		public function saveAtomFile($path_to_media)
		{
				$subdir = Dase_Util::getSubdir($this->serial_number);
				$path = $path_to_media.'/'.$this->p_collection_ascii_id.'/atom/'.$subdir;
				if (!file_exists($path)) {
						mkdir($path);
				}
				$filename = $this->serial_number.'.atom';
				$app_root = '{APP_ROOT}';
				$entry = new Dase_Atom_Entry_Item;
				$entry = $this->injectAtomEntryData($entry,$app_root);
				if (file_put_contents($path.'/'.$filename,$entry->asXml($entry->root))) {
						return $path.'/'.$filename;
				}	
		}

		public static function getByUrl($db,$url)
		{
				//ignores everything but last two sections
				$url = str_replace('.atom','',$url);
				$sections = explode('/',trim($url,'/'));
				$sernum = array_pop($sections);
				$coll = array_pop($sections);
				//will return false if no such item
				return Dase_DBO_Item::get($db,$coll,$sernum);
		}

		public static function getByUnique($db,$unique)
		{
				$sections = explode('/',$unique);
				$sernum = array_pop($sections);
				$coll = array_pop($sections);
				//will return false if no such item
				return Dase_DBO_Item::get($db,$coll,$sernum);
		}

		public function deleteSearchIndex()
		{
				$solr = new Dase_Solr($this->db,$this->config);
				Dase_Log::debug(LOG_FILE,"deleted index for " . $this->serial_number);
				return $solr->deleteItemIndex($this);
		}

		public function deleteDocs()
		{
				$doc = new Dase_DBO_ItemAtom($this->db);
				$doc->unique_id = $this->getUnique();
				if ($doc->findOne()) {
						$doc->delete();
				}
				$doc = new Dase_DBO_ItemJson($this->db);
				$doc->unique_id = $this->getUnique();
				if ($doc->findOne()) {
						$doc->delete();
				}
		}

		public function buildSearchIndex($commit=true,$create_docs=true)
		{
				if ($create_docs) {
						$this->storeDoc();
				}
				$solr = new Dase_Solr($this->db,$this->config);
				Dase_Log::debug(LOG_FILE,"built indexes for " . $this->serial_number);
				return $solr->buildItemIndex($this,$commit);
		}

		public function asAtom($app_root,$as_feed = false)
		{
				$doc = new Dase_DBO_ItemAtom($this->db);
				$doc->unique_id = $this->getUnique();
				if (!$doc->findOne()) {
						$doc->updated = date(DATE_ATOM);
						$entry = new Dase_Atom_Entry_Item;
						$entry = $this->injectAtomEntryData($entry,$app_root);
						$doc->doc = $entry->asXml($entry->root);
						$doc->insert();
				}
				$entry = str_replace('{APP_ROOT}',$app_root,$doc->doc);
				if ($as_feed) {
						$updated = date(DATE_ATOM);
						$id = 'tag:daseproject.org,'.date("Y-m-d").':'.Dase_Util::getUniqueName();
						$feed = <<<EOD
<feed xmlns="http://www.w3.org/2005/Atom"
		xmlns:d="http://daseproject.org/ns/1.0">
	<author>
	<name>DASe (Digital Archive Services)</name>
	<uri>http://daseproject.org</uri>
	<email>admin@daseproject.org</email>
	</author>
	<title>DASe Item as Feed</title>
	<updated>$updated</updated>
	<category term="item" scheme="http://daseproject.org/category/feedtype"/>
	<id>$id</id>
	$entry
</feed>
EOD;
						return $feed;
				}
				return $entry;
		}

		public function asJson($app_root)
		{
				$doc = new Dase_DBO_ItemJson($this->db);
				$doc->unique_id = $this->getUnique();
				if (!$doc->findOne()) {
						$doc->updated = date(DATE_ATOM);
						$doc->doc = $this->buildJson($app_root);
						$doc->insert();
				}
				$item = str_replace('{APP_ROOT}',$app_root,$doc->doc);
				return $item;
		}

		private function _getMetadata()
		{
				if (count($this->_metadata)) {
						return $this->_metadata;
				}
				$prefix = $this->db->table_prefix;
				$metadata = array();
				$bound_params = array();
				$sql = "
						SELECT a.ascii_id, a.id as att_id, a.attribute_name,
						v.value_text,a.collection_id, v.id, 
						a.in_basic_search,a.is_on_list_display, a.is_public,v.url,v.modifier,a.modifier_type,a.html_input_type
						FROM {$prefix}attribute a, {$prefix}value v
						WHERE v.item_id = ?
						AND v.attribute_id = a.id
						ORDER BY a.sort_order,v.value_text
						";
				$dbh = $this->db->getDbh();
				$sth = $dbh->prepare($sql);
				$sth->execute(array($this->id));
				while ($row = $sth->fetch()) {
						$row['edit-id'] =  '/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/metadata/'.$row['id'];
						$metadata[] = $row;
				}
				$this->_metadata = $metadata;
				return $metadata;
		}

		public function getMetadata($include_admin=false,$att_ascii_id='')
		{
				$metadata = array();
				foreach ($this->_getMetadata() as $meta) {
						if ($att_ascii_id && ($meta['ascii_id'] != $att_ascii_id)) {
								break;
						}
						if (0 == $meta['collection_id']) {
								if ($include_admin) {
										$metadata[] = $meta;
								}
						} else {
								$meta['edit-id'] = 
										'/item/'.$this->p_collection_ascii_id.'/'.
										$this->serial_number.'/metadata/'.$meta['id'];
								$metadata[] = $meta;
						}
				}
				return $metadata;
		}

		public function getAdminMetadata($att_ascii_id = '')
		{
				$metadata = array();
				foreach ($this->_getMetadata() as $meta) {
						if (0 == $meta['collection_id']) {
								$metadata[] = $meta;
						}
				}
				return $metadata;
		}

		//used for edit metadata form
		public function getMetadataJson($app_root)
		{
				//clean up to use standard names
				$metadata = array();
				foreach ($this->_getMetadata() as $meta) {
						$set = array();
						$set['value_id'] = $meta['id'];
						$set['url'] = $app_root.$meta['edit-id'];
						$set['collection_id'] = $meta['collection_id'];
						$set['att_ascii_id'] = $meta['ascii_id'];
						$set['attribute_name'] = $meta['attribute_name'];
						$set['html_input_type'] = $meta['html_input_type'];
						$set['value_text'] = $meta['value_text'];
						$set['metadata_link_url'] = $meta['url'];
						$set['modifier'] = $meta['modifier'];
						$set['modifier_type'] = $meta['modifier_type'];
						if (in_array($meta['html_input_type'],
								array('radio','checkbox','select','text_with_menu'))
						) {
								$att = new Dase_DBO_Attribute($this->db);
								$att->load($meta['att_id']);
								$set['values'] = $att->getFormValues();
						}
						$metadata[] = $set;
				}
				return Dase_Json::get($metadata);
		}

		public function getValues()
		{
				$val = new Dase_DBO_Value($this->db);
				$val->item_id = $this->id;
				return $val->find();
		}

		public function getValue($att_ascii_id)
		{
				//only returns first found
				$prefix = $this->db->table_prefix;
				$sql = "
						SELECT v.value_text
						FROM {$prefix}attribute a, {$prefix}value v
						WHERE v.item_id = ?
						AND v.attribute_id = a.id
						AND a.ascii_id = ?
						LIMIT 1
						";
				$dbh = $this->db->getDbh();
				$sth = $dbh->prepare($sql);
				$sth->execute(array($this->id,$att_ascii_id));
				$res = $sth->fetch();
				if ($res && $res['value_text']) {
						return $res['value_text'];
				} else {
						return false;
				}
		}

		public function getCollection()
		{
				//avoids another db lookup
				if ($this->_collection) {
						return $this->_collection;
				}
				$db = $this->db;
				$c = new Dase_DBO_Collection($db);
				$c->load($this->collection_id);
				if ($c) {
						$this->_collection = $c;
						return $c;
				} else {
						return false;
				}
		}

		public function getItemType()
		{
				if ($this->_item_type) {
						return $this->_item_type;
				}
				$db = $this->db;
				$item_type = new Dase_DBO_ItemType($db);
				if ($this->item_type_id) {
						$item_type->load($this->item_type_id);
				} else {
						$item_type->name = 'default';
						$item_type->ascii_id = 'default';
						$item_type->collection_id = $this->collection_id;
				}
				$this->_item_type = $item_type;
				return $item_type;
		}

		public function getMedia()
		{
				if (count($this->_media)) {
						return $this->_media;
				}
				$prefix = $this->db->table_prefix;
				$sql = "
						SELECT * FROM {$prefix}media_file
						WHERE item_id = ?
						ORDER BY file_size ASC 
						";
				$dbh = $this->db->getDbh();
				$sth = $dbh->prepare($sql);
				$sth->execute(array($this->id));
				$last = null;
				while ($m = $sth->fetch()) {
						$m['url'] = 
								'/media/'.$m['p_collection_ascii_id'].
								'/'.$m['size'].'/'.$m['filename'];
						$this->_media[$m['size']] = $m;
						$last = $m;
				}

				//last, biggest media 
				if ($last) {
						$this->_media['enclosure'] = $last;
				}

				//auto-set thumbnail && viewitem
				if (!isset($this->_media['thumbnail'])) {
						$m['url'] = 
								'/media/'.$this->p_collection_ascii_id.
								'/thumbnail/_default.jpg';
						$m['height'] = 60;
						$m['width'] = 60;
						$m['mime_type'] = 'image/jpeg';
						$m['file_size'] = '1118';
						$this->_media['thumbnail'] = $m;
				}

				if (!isset($this->_media['viewitem'])) {
						$m['url'] = 
								'/media/'.$this->p_collection_ascii_id.
								'/viewitem/_default.jpg';
						$m['height'] = 60;
						$m['width'] = 60;
						$m['mime_type'] = 'image/jpeg';
						$m['file_size'] = '1118';
						$this->_media['viewitem'] = $m;
				}
				return $this->_media;
		}

		public function getMediaUrl($size,$app_root,$token = '')
		{
				$med_array = $this->getMedia(); 
				if (isset($med_array[$size])) {
						$m = $med_array[$size];
						$url = $app_root.$m['url'];
						if ($token) {
								$expires = time() + (60*60); 
								$auth_token = md5($url.$expires.$token);
								$url = $url.'?auth_token='.$auth_token.'&'.'expires='.$expires;
						}
						return $url;
				}
		}

		function getMediaCount()
		{
				return count($this->getMedia());
		}

		function setItemType($type_ascii_id='')
		{
				if (!$type_ascii_id || 'none' == $type_ascii_id || 'default' == $type_ascii_id) {
						$this->item_type_id = 0;
						$this->item_type_ascii_id = 'default';
						$this->item_type_name = 'default';
						$this->updated = date(DATE_ATOM);
						$this->update();
						return true;
				}
				$type = new Dase_DBO_ItemType($this->db);
				$type->ascii_id = $type_ascii_id;
				$type->collection_id = $this->collection_id;
				if ($type->findOne()) {
						$this->item_type_id = $type->id;
						$this->item_type_ascii_id = $type->ascii_id;
						$this->item_type_name = $type->name;
						$this->updated = date(DATE_ATOM);
						$this->update();
						$this->_item_type = $type;
						return true;
				} else {
						//now w/ auto-create 3/18/2011
						$type->name = $type_ascii_id;
						$type->insert();
						$this->item_type_id = $type->id;
						$this->item_type_ascii_id = $type->ascii_id;
						$this->item_type_name = $type->name;
						$this->updated = date(DATE_ATOM);
						$this->update();
						$this->_item_type = $type;
						return true;
				}
		}

		function updateMetadata($value_id,$value_text,$eid,$modifier='',$index=true)
		{
				$v = new Dase_DBO_Value($this->db);
				$v->load($value_id);
				$att = $v->getAttribute();
				if ($modifier) {
						// a bit of a hack. to delete modifier
						// you need to pass in '_delete'
						if ('_delete' == $modifier) {
								$v->modifier = '';
						} else {
								$v->modifier = $modifier;
						}
				}
				$v->value_text = $value_text;
				$v->update();
				if ($index) {
						$this->updated = date(DATE_ATOM);
						$this->update();
						$this->buildSearchIndex();
				}
		}

		function removeKeyval($att_ascii_id,$value_text,$eid)
		{
				$c = $this->getCollection();
				$att = Dase_DBO_Attribute::get($this->db,$c->ascii_id,$att_ascii_id);
				if ($att) {
						$val = new Dase_DBO_Value($this->db);
						$val->item_id = $this->id;
						$val->attribute_id = $att->id;
						$val->value_text = $value_text;
						if ($val->findOne()) {
								$this->removeMetadata($val->id,$eid);
								return true;
						}
				}
		}

		function removeMetadata($value_id,$eid,$index=true)
		{
				$v = new Dase_DBO_Value($this->db);
				$v->load($value_id);
				$att = $v->getAttribute();
				$v->delete();
				if ($index) {
						$this->updated = date(DATE_ATOM);
						$this->update();
						$this->buildSearchIndex();
				}
		}

		/** simple convenience method */
		function updateTitle($value_text,$eid,$index=true)
		{
				$att = Dase_DBO_Attribute::findOrCreate($this->db,$this->p_collection_ascii_id,'title');
				if ($att) {
						$v = new Dase_DBO_Value($this->db);
						$v->item_id = $this->id;
						$v->attribute_id = $att->id;
						if ($v->findOne()) {
								$v->value_text = trim($value_text);
								$v->update();
						} else {
								$v->value_text = trim($value_text);
								$v->insert();
						}
						if ($index) {
								$this->updated = date(DATE_ATOM);
								$this->update();
								$this->buildSearchIndex();
						}
				}
		}

		function setValue($att_ascii_id,$value_text,$url='',$modifier='',$index=false)
		{
				if (!trim($att_ascii_id) || (!trim($value_text) && "0" !== $value_text)) {
						return false;
				}
				//allows for admin metadata, att_ascii for which always begins 'admin_'
				//NOTE: we DO create att if it does not exist
				if (false === strpos($att_ascii_id,'admin_')) {
						$att = Dase_DBO_Attribute::findOrCreate($this->db,$this->p_collection_ascii_id,$att_ascii_id);
				} else {
						$att = Dase_DBO_Attribute::findOrCreateAdmin($this->db,$att_ascii_id);
				}
				if ($att) {
						if ('listbox' == $att->html_input_type) {
								//never includes url or modifier
								$pattern = '/[\n;]/';
								$prepared_string = preg_replace($pattern,'%',trim($value_text));
								$values_array = explode('%',$prepared_string);
								foreach ($values_array as $val_txt) {
										$v = new Dase_DBO_Value($this->db);
										$v->item_id = $this->id;
										$v->attribute_id = $att->id;
										$v->value_text = $val_txt;
										//note: duplicate detection
										//added 4/9/2010
										if (!$v->findOne()) {
												$v->insert();
										}
								}
						} else {
								$v = new Dase_DBO_Value($this->db);
								$v->item_id = $this->id;
								$v->attribute_id = $att->id;
								$v->value_text = trim($value_text);
								$v->url = $url;
								$v->modifier = $modifier;
								//note: duplicate detection
								//added 4/9/2010
								if (!$v->findOne()) {
										$v->insert();
								}
								if ($index) {
										$this->updated = date(DATE_ATOM);
										$this->update();
										$this->buildSearchIndex();
								}
								return $v;
						}
						if ($index) {
								$this->updated = date(DATE_ATOM);
								$this->update();
								$this->buildSearchIndex();
						}
				} else {
						//simply returns false if no such attribute
						Dase_Log::debug(LOG_FILE,'[WARNING] no such attribute '.$att_ascii_id);
						return false;
				}
		}

		function setValueLink($att_ascii_id,$value_text,$url,$modifier='',$index=true)
		{
				return $this->setValue($att_ascii_id,$value_text,$url,$modifier,$index);
		}


		/** deletes non-admin values including those with urls (metadata-links) */
		function deleteValues($index=false)
		{
				//should sanity check and archive values
				$admin_ids = Dase_DBO_Attribute::listAdminAttIds($this->db);
				$v = new Dase_DBO_Value($this->db);
				$v->item_id = $this->id;
				foreach ($v->find() as $doomed) {
						//do not delete admin att values
						if (!in_array($doomed->attribute_id,$admin_ids)) {
								$doomed->delete();
						}
				}
				if ($index) {
						$this->updated = date(DATE_ATOM);
						$this->update();
						$this->buildSearchIndex();
				}
		}

		function deleteAdminValues()
		{
				$a = new Dase_DBO_Attribute($this->db);
				$a->collection_id = 0;
				foreach ($a->find() as $aa) {
						$v = new Dase_DBO_Value($this->db);
						$v->item_id = $this->id;
						$v->attribute_id = $aa->id;
						foreach ($v->find() as $doomed) {
								$doomed->delete();
						}
				}
				return "deleted admin metadata for " . $this->serial_number . "\n";
		}

		function saveCopy($path_to_media)
		{
				$now = time();
				$filename = $path_to_media.'/'.$this->p_collection_ascii_id.'/deleted/'.$this->serial_number.'.'.$now.'.json';
				file_put_contents($filename,$this->asJson('http://daseproject.org'));
		}

		function storeDoc($type="")
		{
				$app_root="{APP_ROOT}";

				if (!$type || 'json' == $type) {
						$doc = new Dase_DBO_ItemJson($this->db);
						$doc->unique_id = $this->getUnique();
						if ($doc->findOne()) {
								$doc->updated = date(DATE_ATOM);
								$doc->doc = $this->buildJson($app_root);
								$doc->update();
						} else {
								$doc->updated = date(DATE_ATOM);
								$doc->doc = $this->buildJson($app_root);
								$doc->insert();
						}
				}

				if (!$type || 'atom' == $type) {
						$entry = new Dase_Atom_Entry_Item;
						$entry = $this->injectAtomEntryData($entry,$app_root);
						$doc = new Dase_DBO_ItemAtom($this->db);
						$doc->unique_id = $this->getUnique();
						if ($doc->findOne()) {
								$doc->updated = date(DATE_ATOM);
								//passing in entry root prevent xml declaration
								$doc->doc = $entry->asXml($entry->root);
								$doc->update();
						} else {
								$doc->updated = date(DATE_ATOM);
								$doc->doc = $entry->asXml($entry->root);
								$doc->insert();
						}
				}
		}

		function expunge($path_to_media='')
		{
				if ($path_to_media) {
						$this->saveCopy($path_to_media);
				}
				$c = $this->getCollection();
				$sernum = $this->serial_number;

				$this->deleteMedia($path_to_media);
				$this->deleteValues();
				$this->deleteAdminValues();
				$this->deleteSearchIndex();
				$this->deleteDocs();
				$this->deleteComments();
				$this->deleteTagItems();
				$this->delete();
				$c->updateItemCount();
				return "expunged item ".$sernum;
		}

		function deleteComments()
		{
				$co = new Dase_DBO_Comment($this->db);
				$co->item_id = $this->id;
				foreach ($co->find() as $doomed) {
						$doomed->delete();
				}
		}

		function deleteTagItems()
		{
				$tag_item = new Dase_DBO_TagItem($this->db);
				$tag_item->item_id = $this->id;
				$tags = array();
				foreach ($tag_item->find() as $doomed) {
						$tag = $doomed->getTag();
						$doomed->delete();
						$tag->updateItemCount();
				}
		}

		function deleteMedia($path_to_media='')
		{
				$mf = new Dase_DBO_MediaFile($this->db);
				$mf->item_id = $this->id;
				foreach ($mf->find() as $doomed) {
						if ($path_to_media) {
								$doomed->moveFileToDeleted($path_to_media);
						}
						$doomed->delete();
				}
		}

		function getTitle()
		{
				$prefix = $this->db->table_prefix;
				$sql = "
						SELECT v.value_text 
						FROM {$prefix}attribute a, {$prefix}value v
						WHERE a.id = v.attribute_id
						AND a.ascii_id = 'title'
						AND v.item_id = ? 
						";
				$dbh = $this->db->getDbh();
				$sth = $dbh->prepare($sql);
				$sth->execute(array($this->id));
				$title = $sth->fetchColumn();
				if (!$title) {
						$title = $this->serial_number;
				}
				return $title;
		}

		function getDescription()
		{
				$prefix = $this->db->table_prefix;
				$sql = "
						SELECT v.value_text 
						FROM {$prefix}attribute a, {$prefix}value v
						WHERE a.id = v.attribute_id
						AND a.ascii_id = 'description'
						AND v.item_id = ? 
						";
				$dbh = $this->db->getDbh();
				$sth = $dbh->prepare($sql);
				$sth->execute(array($this->id));
				$description = $sth->fetchColumn();
				if (!$description) {
						$description = $this->getTitle();
				}
				return $description;
		}

		function getRights()
		{
				$prefix = $this->db->table_prefix;
				$sql = "
						SELECT v.value_text 
						FROM {$prefix}attribute a, {$prefix}value v
						WHERE a.id = v.attribute_id
						AND a.ascii_id = 'rights'
						AND v.item_id = ? 
						";
				$dbh = $this->db->getDbh();
				$sth = $dbh->prepare($sql);
				$sth->execute(array($this->id));
				$text = $sth->fetchColumn();
				if (!$text) { $text = 'daseproject.org'; }
				return $text;
		}

		public function getEnclosure()
		{
				$media = $this->getMedia();
				if (isset($media['enclosure'])) {
						return $media['enclosure'];
				}
		}


		function injectAtomEntryData(Dase_Atom_Entry $entry,$app_root,$authorize_links=false)
		{
				if (!$this->id) { return false; }

				/* namespaces */

				$d = Dase_Atom::$ns['d'];
				$thr = Dase_Atom::$ns['thr'];

				/* resources */

				$base_url = $app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number;

				/* standard atom stuff */

				$entry->setId($base_url);

				$entry->addAuthor($this->created_by_eid);
				//todo: I think this can be simplified when DASe 1.0 is retired
				if (is_numeric($this->updated)) {
						$entry->setUpdated(date(DATE_ATOM,$this->updated));
				} else {
						$entry->setUpdated($this->updated);
				}
				if (is_numeric($this->created)) {
						$entry->setPublished(date(DATE_ATOM,$this->created));
				} else {
						$entry->setPublished($this->created);
				}

				//atompub
				$entry->setEdited($entry->getUpdated());

				//alternate link
				$entry->addLink($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number,'alternate');

				//the following 2 links should be unified

				//link to item metadata json, used for editing metadata
				$entry->addLink($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/metadata.json',
						'http://daseproject.org/relation/metadata','application/json');

				//to which we can POST form-encoded or json metadata pairs:
				$entry->addLink($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/metadata',
						'http://daseproject.org/relation/edit-metadata');

				//to which we can POST form-encoded or text/plain:
				$entry->addLink($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/item_type',
						'http://daseproject.org/relation/edit-item_type');

				$entry->addLink(
						$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'.atom',
						'edit','application/atom+xml');
				if ($authorize_links) {
						$entry->addLink(
								$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'.atom',
								'http://daseproject.org/relation/cached','application/atom+xml');
				} else {
						$entry->addLink(
								$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/authorized.atom',
								'http://daseproject.org/relation/authorized','application/atom+xml');
				}
				$entry->addLink(
						$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/content',
						'http://daseproject.org/relation/edit-content');
				$entry->addLink(
						$app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'.json',
						'http://daseproject.org/relation/edit','application/json');
				$entry->addLink(
						$app_root.'/collection/'.$this->p_collection_ascii_id.'/service',
						'service','application/atomsvc+xml');
				$entry->addLink(
						$app_root.'/collection/'.$this->p_collection_ascii_id.'/attributes.json',
						'http://daseproject.org/relation/attributes',
						'application/json');

				/**** COMMENT LINK (threading extension) **********/

				$replies = $entry->addLink($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/comments','replies' );
				if ($this->comments_count) {
						//lookup
						$replies->setAttributeNS($thr,'thr:count',$this->comments_count);
						//lookup
						$replies->setAttributeNS($thr,'thr:updated',$this->comments_updated);
				}

				/* dase categories */

				$entry->setEntrytype('item');

				//allows us to replace all if/when necessary :(
				$entry->addCategory($app_root,"http://daseproject.org/category/base_url");

				$entry->addCategory($this->item_type_ascii_id,'http://daseproject.org/category/item_type',$this->item_type_name);
				$entry->addCategory($this->p_collection_ascii_id,'http://daseproject.org/category/collection',$this->collection_name);
				$entry->addCategory($this->id,'http://daseproject.org/category/item_id');
				$entry->addCategory($this->serial_number,'http://daseproject.org/category/serial_number');

				if ($this->status) {
						$entry->addCategory($this->status,'http://daseproject.org/category/status');
				} else {
						$entry->addCategory('public','http://daseproject.org/category/status');
				}

				/********* METADATA **********/

				$item_metadata = $this->getMetadata(true);
				foreach ($item_metadata as $row) {
						if ($row['url']) { //create metadata LINK
								$metadata_link = $entry->addLink(
										$row['url'],
										'http://daseproject.org/relation/metadata-link/'.
										$this->p_collection_ascii_id.'/'.$row['ascii_id'],
										'',
										'',
										$row['value_text']
								);
								$metadata_link->setAttributeNS($d,'d:attribute',$row['attribute_name']);
								$metadata_link->setAttributeNS($d,'d:edit-id',$app_root.$row['edit-id']);
								if ($row['modifier']) {
										$metadata_link->setAttributeNS($d,'d:mod',$row['modifier']);
										if ($row['modifier_type']) {
												$metadata_link->setAttributeNS($d,'d:modtype',$row['modifier_type']);
										}
								}
						} 
				}
				foreach ($item_metadata as $row) {
						if ($row['url']) { 
								//already made metadata links
						} else { //create metadata CATEGORY
								if (0 == $row['collection_id']) {
										$meta = $entry->addCategory(
												$row['ascii_id'],'http://daseproject.org/category/admin_metadata',
												$row['attribute_name'],$row['value_text']);
								} else {
										if ($row['is_public']) {
												$meta = $entry->addCategory(
														$row['ascii_id'],'http://daseproject.org/category/metadata',
														$row['attribute_name'],$row['value_text']);
												$meta->setAttributeNS($d,'d:edit-id',$app_root.$row['edit-id']);
										} else {
												$meta = $entry->addCategory(
														$row['ascii_id'],'http://daseproject.org/category/private_metadata',
														$row['attribute_name'],$row['value_text']);
												$meta->setAttributeNS($d,'d:edit-id',$app_root.$row['edit-id']);
										}
										if ('title' == $row['ascii_id'] || 'Title' == $row['attribute_name']) {
												$entry->setTitle($row['value_text']);
										}
										if ('rights' == $row['ascii_id']) {
												$entry->setRights($row['value_text']);
										}
										if ($row['modifier']) {
												$meta->setAttributeNS($d,'d:mod',$row['modifier']);
												if ($row['modifier_type']) {
														$meta->setAttributeNS($d,'d:modtype',$row['modifier_type']);
												}
										}
								}
						}
				}

				//this will only "take" if there is not already a title
				$entry->setTitle($this->serial_number);

				/*******  MEDIA  ***********/

				$item_media = $this->getMedia();
				$token = $this->config->getAuth('token');

				if (isset($item_media['enclosure'])) {
						$enc = $item_media['enclosure'];
						if ($authorize_links) {
								$entry->addLink($this->getMediaUrl('enclosure',$app_root,$token),'enclosure',$enc['mime_type'],$enc['file_size']);
						} else {
								$entry->addLink($this->getMediaUrl('enclosure',$app_root),'enclosure',$enc['mime_type'],$enc['file_size']);
						}
				}

				/* edit-media link */

				$entry->addLink($this->getEditMediaUrl($app_root),'edit-media');
				$media_url = $app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/media';
				$entry->addLink($media_url,'http://daseproject.org/relation/add-media');

				/* media rss ext */

				foreach ($this->getMedia() as $size => $med) {
						if ('thumbnail' == $size) {
								$media_thumbnail = $entry->addElement('media:thumbnail','',Dase_Atom::$ns['media']);
								$media_thumbnail->setAttribute('url',$app_root.$med['url']);
								$media_thumbnail->setAttribute('width',$med['width']);
								$media_thumbnail->setAttribute('height',$med['height']);
						} else {
								if ($size != 'enclosure') {
										$media_content = $entry->addElement('media:content','',Dase_Atom::$ns['media']);
										if ($authorize_links) {
												$media_content->setAttribute('url',$this->getMediaUrl($size,$app_root,$token));
										} else {
												$media_content->setAttribute('url',$this->getMediaUrl($size,$app_root));
										}
										if ($med['width'] && $med['height']) {
												$media_content->setAttribute('width',$med['width']);
												$media_content->setAttribute('height',$med['height']);
										}
										$media_content->setAttribute('fileSize',$med['file_size']);
										$media_content->setAttribute('type',$med['mime_type']);
										$media_category = $media_content->appendChild($entry->dom->createElement('media:category'));
										$media_category->appendChild($entry->dom->createTextNode($size));
								}
						}
				}
				return $entry;
		}

		function injectAtomFeedData(Dase_Atom_Feed $feed,$app_root)
		{
				if (!$this->id) { return false; }
				$c = $this->getCollection();
				if (is_numeric($this->updated)) {
						$updated = date(DATE_ATOM,$this->updated);
				} else {
						$updated = $this->updated;
				}
				$feed->setUpdated($updated);
				$feed->setTitle($this->getTitle());
				$feed->setId('tag:daseproject.org,2008:'.Dase_Util::getUniqueName());
				$feed->addLink($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'.atom','self' );
				$feed->addAuthor();
				return $feed;
		}

		public function buildJson($app_root)
		{
				$json_doc = array();
				$json_doc['app_root'] = $app_root; 
				$json_doc['id'] = $app_root.'/'.$this->p_collection_ascii_id.'/'.$this->serial_number;
				$json_doc['item_unique'] = $this->p_collection_ascii_id.'/'.$this->serial_number;
				$json_doc['created'] = $this->created;
				$json_doc['updated'] = $this->updated;
				$json_doc['item_id'] = $this->id;
				$json_doc['serial_number'] = $this->serial_number;
				$json_doc['c'] = $this->p_collection_ascii_id;
				$json_doc['collection'] = $this->collection_name;
				$json_doc['item_type'] = $this->item_type_ascii_id;
				$json_doc['item_type_name'] = $this->item_type_name;
				$json_doc['media'] = array();

				foreach ($this->getMedia() as $sz => $info) {
						if ('enclosure' == $sz) {
								$json_doc['enclosure']["href"] = $info['url'];
								$json_doc['enclosure']["type"] = $info['mime_type'];
								$json_doc['enclosure']["length"] = $info['file_size'];
								if ($info['height'] && $info['width']) {
										$json_doc['enclosure']["height"] = $info['height'];
										$json_doc['enclosure']["width"] = $info['width'];
								}
								if ($info['md5']) {
										$json_doc['enclosure']["md5"] = $info['md5'];
								}
						} else {
								$json_doc['media'][$sz] = $info['url'];
						}
				}

				$json_doc['links'] = array();
				$json_doc['links']['comments'] =  '/item/'.$this->getUnique().'/comments';
				$json_doc['links']['edit'] = '/item/'.$this->getUnique().'.json';
				$json_doc['links']['edit-media'] = '/media/'.$this->getUnique();
				$json_doc['links']['item_type'] =  '/item/'.$this->getUnique().'/item_type';
				$json_doc['links']['media'] =  '/item/'.$this->getUnique().'/media';
				$json_doc['links']['metadata'] =  '/item/'.$this->getUnique().'/metadata';
				$json_doc['links']['status'] =  '/item/'.$this->getUnique().'/status';
				$json_doc['links']['updater'] =  '/item/'.$this->getUnique().'/updater';
				$json_doc['links']['profile'] = '/collection/'.$this->p_collection_ascii_id.'/profile.json';
				$json_doc['alternate'] = array();
				$json_doc['alternate']['html'] =  '/item/'.$this->getUnique().'.html';
				$json_doc['alternate']['atom'] =  '/item/'.$this->getUnique().'.atom';
				$json_doc['alternate']['json'] =  '/item/'.$this->getUnique().'.json';

				$json_doc['metadata'] = array();
				$json_doc['metadata_extended'] = array();

				foreach ($this->getMetadata(true) as $meta) {

						if (0 == $meta['collection_id']) {
								//admin metadata 
								$json_doc[$meta['ascii_id']] = $meta['value_text'];
						} else {
								$json_doc['metadata'][$meta['ascii_id']][] = $meta['value_text'];
								$json_doc['metadata_extended'][$meta['ascii_id']]['label'] = $meta['attribute_name'];
								if (!isset($json_doc['metadata_extended'][$meta['ascii_id']]['values'])) {
										$json_doc['metadata_extended'][$meta['ascii_id']]['values'] = array();
								}
								$value_set = array();
								$value_set['text'] = $meta['value_text'];
								if ($meta['url']) {
										$value_set['url'] = $meta['url'];
								}
								if ($meta['modifier']) {
										$value_set['modifier'] = $meta['modifier'];
								}
								if ($meta['edit-id']) {
										$value_set['edit'] = $meta['edit-id'];
								}
								$json_doc['metadata_extended'][$meta['ascii_id']]['values'][] = $value_set;
						}
				}
				return Dase_Json::get($json_doc);
		}

		function asAtomEntry($app_root="{APP_ROOT}",$authorize_links=false)
		{
				if ($authorize_links) {
						$entry = new Dase_Atom_Entry_Item;
						$entry = $this->injectAtomEntryData($entry,$app_root,true);
						return $entry->asXml();
				} else {
						return $this->asAtom($app_root);
				}
		}

		/** todo this does NOT work */
		function mediaAsAtomFeed($app_root) 
		{
				$feed = new Dase_Atom_Feed;
				$this->injectAtomFeedData($feed,$app_root);
				foreach ($this->getMedia('updated DESC') as $m) {
						$entry = $feed->addEntry();
						$m->injectAtomEntryData($entry,$app_root);
				}
				return $feed->asXml();
		}	

		public function getUrl($app_root) 
		{
				return $app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number;
		}

		public function getUnique() 
		{
				if (!$this->p_collection_ascii_id) {
						$this->p_collection_ascii_id = $this->getCollection()->ascii_id;
						$this->update();
				}
				return $this->p_collection_ascii_id.'/'.$this->serial_number;
		}

		public function getEditMediaUrl($app_root='')
		{
				return $app_root.'/media/'.$this->p_collection_ascii_id.'/'.$this->serial_number;
		}

		public function getAtomPubServiceDoc($app_root) {
				$c = $this->getCollection();
				$app = new Dase_Atom_Service;
				$workspace = $app->addWorkspace($c->collection_name.' Item '.$this->serial_number.' Workspace');
				$media_coll = $workspace->addCollection($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/media.atom',$c->collection_name.' Item '.$this->serial_number.' Media'); 
				foreach(Dase_Media::getAcceptedTypes() as $type) {
						$media_coll->addAccept($type);
				}
				$comments_coll = $workspace->addCollection($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/comments.atom',$c->collection_name.' Item '.$this->serial_number.' Comments'); 
				$comments_coll->addAccept('text/plain');
				$comments_coll->addAccept('text/html');
				$comments_coll->addAccept('application/xhtml+xml');
				$metadata_coll = $workspace->addCollection($app_root.'/item/'.$this->p_collection_ascii_id.'/'.$this->serial_number.'/metadata.atom',$c->collection_name.' Item '.$this->serial_number.' Metadata'); 
				$metadata_coll->addAccept('application/x-www-form-urlencoded');
				$metadata_coll->addAccept('application/json');
				return $app->asXml();
		}

		public function statusAsJson()
		{
				$labels['public'] = "Public";
				$labels['draft'] = "Draft (Admin View Only)";
				$labels['delete'] = "Marked for Deletion";
				$labels['archive'] = "In Deep Storage";

				$status['term'] = $this->status;
				$status['label'] = $labels[$this->status];

				return Dase_Json::get($status);
		}

		public function getCommentsJson($app_root,$eid='')
		{
				$db = $this->db;
				$comments = new Dase_DBO_Comment($db);
				$comments->item_id = $this->id;
				$comments->updated_by_eid = $eid;
				$comments->orderBy('updated DESC');
				$com = array();
				foreach ($comments->find() as $c_obj) {
						$c['id'] = $c_obj->id;
						//$c['updated'] = $c_obj->updated;
						$c['updated'] = date('D M j, Y \a\t g:ia',strtotime($c_obj->updated));
						$c['eid'] = $c_obj->updated_by_eid;
						$c['text'] = $c_obj->text;
						$c['url'] = $this->getUrl($app_root).'/comments/'.$c_obj->id;
						$com[] = $c;
				}
				return Dase_Json::get($com);
		}

		public function addComment($text,$eid)
		{
				$note = new Dase_DBO_Comment($this->db);
				$note->item_id = $this->id;
				//todo: security! filter input....
				$note->text = $text;
				$note->p_collection_ascii_id = $this->p_collection_ascii_id;
				$note->p_serial_number = $this->serial_number;
				$note->updated = date(DATE_ATOM);
				$note->updated_by_eid = $eid;
				$res = $note->insert();
				//denormalization
				$this->comments_count = $this->comments_count+1;
				$this->comments_updated = $note->updated;
				$this->update();
				$this->buildSearchIndex();
				return $res;
		}

		public function getTags()
		{
				$tags = array();
				$tag_item = new Dase_DBO_TagItem($this->db);
				$tag_item->item_id = $this->id;
				foreach ($tag_item->find() as $ti) {
						$tags[] = $ti->getTag();
				}
				if (count($tags)) {
						return $tags;
				} else {
						return false;
				}
		}

		public static function sortIdArrayByUpdated($db,$item_ids)
		{
				$sortable_array = array();
				$prefix = $db->table_prefix;
				$dbh = $db->getDbh();
				$sql = "
						SELECT updated 
						FROM {$prefix}item i
						WHERE i.id = ? 
						";
				$sth = $dbh->prepare($sql);
				foreach ($item_ids as $item_id) {
						$sth->execute(array($item_id));
						$updated = $sth->fetchColumn();
						$sortable_array[$item_id] = $updated;
				}
				if (is_array($sortable_array)) {
						arsort($sortable_array);
						return array_keys($sortable_array);
				}
		}

		public static function sortIdArray($db,$sort,$item_ids)
		{
				$sortable_array = array();
				$test_att = new Dase_DBO_Attribute($db);
				$test_att->ascii_id = $sort;
				if (!$test_att->findOne()) {
						return $item_ids;
				}
				$prefix = $db->table_prefix;
				$dbh = $db->getDbh();
				$sql = "
						SELECT v.value_text
						FROM {$prefix}attribute a, {$prefix}value v
						WHERE v.item_id = ?
						AND v.attribute_id = a.id
						AND a.ascii_id = ?
						LIMIT 1
						";
				$sth = $dbh->prepare($sql);
				foreach ($item_ids as $item_id) {
						$sth->execute(array($item_id,$sort));
						$vt = $sth->fetchColumn();
						$value_text = $vt ? $vt : 99999999;
						$sortable_array[$item_id] = $value_text;
				}
				if (is_array($sortable_array)) {
						asort($sortable_array);
						return array_keys($sortable_array);
				}
		}

		/** expires any cache that might hold stale metadata */
		public function expireCaches($cache)
		{
				//more will (perhaps) go here
				//
				// attributes json (includes tallies)
				$cache_id = "get|collection/".$this->p_collection_ascii_id."/attributes/public/tallies|json|cache_buster=stripped&format=json";
				$cache->expire($cache_id);

		}

		public function mapConfiguredAdminAtts()
		{
				$c = $this->getCollection();
				foreach ($c->getAttributes() as $att) {
						if ($att->mapped_admin_att_id) {
								foreach ($this->getAdminMetadata() as $row) {
										if ($att->mapped_admin_att_id == $row['att_id']) {
												$this->setValue($att->ascii_id,$row['value_text']);
										}
								}
						}
				}
		}
}
