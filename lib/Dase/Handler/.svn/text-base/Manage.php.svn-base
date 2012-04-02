<?php

class Dase_Handler_Manage extends Dase_Handler
{
	public $collection;
	public $resource_map = array(
		'{collection_ascii_id}' => 'uploader',
		'{collection_ascii_id}/remote_acl' => 'remote_acl',
		'{collection_ascii_id}/attribute_form' => 'attribute_form',
		'{collection_ascii_id}/attribute/{att_ascii_id}' => 'attribute',
		'{collection_ascii_id}/attribute/{att_ascii_id}/defined_values' => 'attribute_defined_values',
		'{collection_ascii_id}/attributes' => 'attributes',
		'{collection_ascii_id}/item_types' => 'item_types',
		'{collection_ascii_id}/index_update' => 'index_update',
		'{collection_ascii_id}/delete_items' => 'delete_items',
		'{collection_ascii_id}/item_type_form' => 'item_type_form',
		'{collection_ascii_id}/item_type/{type_ascii_id}' => 'item_type',
		'{collection_ascii_id}/item_type/{type_ascii_id}/attributes' => 'item_type_attributes',
		'{collection_ascii_id}/item_type/{type_ascii_id}/attribute/{att_ascii_id}' => 'item_type_attribute',
		'{collection_ascii_id}/managers' => 'managers',
		'{collection_ascii_id}/managers/{manager_eid}' => 'manager',
		'{collection_ascii_id}/settings' => 'settings',
		'{collection_ascii_id}/indexer' => 'indexer',
		'{collection_ascii_id}/uploader' => 'uploader',
		'{collection_ascii_id}/attributes/{filter}' => 'attributes',
	);

	protected function setup($r)
	{
		$this->collection = Dase_DBO_Collection::get($this->db,$r->get('collection_ascii_id'));
		if (!$this->collection) {
			$r->renderError(404);
		}
		$this->user = $r->getUser();
		if (!$this->user->can('admin',$this->collection)) {
			$r->renderError(401);
		}
		//so proper menu item highlights
		$r->set('tab',$r->resource);
	}

	public function getSettings($r)
	{
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$r->renderResponse($tpl->fetch('manage/settings.tpl'));
	}

	public function postToSettings($r)
	{
		$this->collection->collection_name = trim($r->get('collection_name'));
		//uses false because you cannot pass a zero as a value through form (dase framework bug)
		if ('false' == $r->get('is_public')) {
			$this->collection->is_public = 0;
		} else {
			$this->collection->is_public = 1;
		}
		$this->collection->remote_media_host = trim($r->get('remote_media_host'));
		$this->collection->description = trim($r->get('description'));
		$this->collection->admin_notes = trim($r->get('admin_notes'));
		$this->collection->visibility = $r->get('visibility');
		$this->collection->update();
		$params['msg'] = "settings updated";
		$this->user->expireDataCache($r->getCache());
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/settings',$params);
	}

	public function getAttributes($r)
	{
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('attributes',$this->collection->getAttributes());
		$r->renderResponse($tpl->fetch('manage/attribute_form.tpl'));
	}

	public function getAttribute($r)
	{
		$att = Dase_DBO_Attribute::get($this->db,$this->collection->ascii_id,$r->get('att_ascii_id'));
		if (!$att) {
			$r->renderError(404,'so such attribute');
		}
		$tpl = new Dase_Template($r);
		$tpl->assign('admin_atts',$this->collection->getAdminAttributes());
		$tpl->assign('ordered',$this->collection->getAttributesSortedArray());
		$tpl->assign('collection',$this->collection);
		$tpl->assign('attributes',$this->collection->getAttributes());
		$tpl->assign('item_types',$att->getItemTypes());
		$tpl->assign('att',$att);
		$r->set('tab','attributes');
		$r->renderResponse($tpl->fetch('manage/attribute_form.tpl'));
	}

	public function getAttributeForm($r)
	{
		$att = new Dase_DBO_Attribute($this->db);

		//since this is new:
		$att->is_public = 1;
		$att->in_basic_search = 1;
		$att->is_on_list_display = 1;
		$att->is_repeatable = 1;

		$tpl = new Dase_Template($r);
		$tpl->assign('admin_atts',$this->collection->getAdminAttributes());
		$tpl->assign('ordered',$this->collection->getAttributesSortedArray());
		$tpl->assign('collection',$this->collection);
		$tpl->assign('attributes',$this->collection->getAttributes());
		$tpl->assign('att',$att);
		$r->set('tab','attributes');
		$r->renderResponse($tpl->fetch('manage/attribute_form.tpl'));
	}

	public function deleteManager($r)
	{
		$manager = new Dase_DBO_CollectionManager($this->db);
		if ($r->get('manager_eid') == $this->user->eid) {
			$r->renderError('400','existential crisis: cannot delete yourself');
		}
		$manager->dase_user_eid = $r->get('manager_eid');
		$manager->collection_ascii_id = $this->collection->ascii_id;
		$manager->findOne();
		$eid = $manager->dase_user_eid;
		if ($manager->id && $manager->dase_user_eid && $manager->collection_ascii_id) {
			$manager->delete();
		}
		$r->renderResponse('deleted manager '.$eid);
	}


	public function postToAttribute($r)
	{
		$att = Dase_DBO_Attribute::get($this->db,$this->collection->ascii_id,$r->get('att_ascii_id'));
		if ($r->has('method') && ('delete '.$att->attribute_name == $r->get('method'))) {
			$d = $att->attribute_name;
			$count = count($att->getCurrentValues());
			if ($count) {
				$params['msg'] = "sorry, but there are $count values for $att->attribute_name so it cannot be deleted";
				$r->renderRedirect('manage/'.$this->collection->ascii_id.'/attributes',$params);
			}
			$att->expunge();
			$att->resort();
			$params['msg'] = "$d deleted";
			$r->renderRedirect('manage/'.$this->collection->ascii_id.'/attributes',$params);
		}
		$att->attribute_name = $r->get('attribute_name');
		$att->modifier_type = $r->get('modifier_type');
		$att->usage_notes = $r->get('usage_notes');
		$att->modifier_defined_list = $r->get('modifier_defined_list');
		$att->mapped_admin_att_id = $r->get('mapped_admin_att_id') ? $r->get('mapped_admin_att_id') : 0;
		if ($r->has('is_on_list_display')) {
			$att->is_on_list_display = 1;
		} else {
			$att->is_on_list_display = 0;
		}
		if ($r->has('in_basic_search')) {
			$att->in_basic_search = 1;
		} else {
			$att->in_basic_search = 0;
		}
		if ($r->has('is_public')) {
			$att->is_public = 1;
		} else {
			$att->is_public = 0;
		}
		if ($r->has('is_repeatable')) {
			$att->is_repeatable = 1;
		} else {
			$att->is_repeatable = 0;
		}
		if ($r->has('is_required')) {
			$att->is_required = 1;
		} else {
			$att->is_required = 0;
		}
		$att->html_input_type = $r->get('input_type');
		$att->update();
		$att->resort($r->get('sort'));
		$params['msg'] = "$att->attribute_name updated";
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/attribute/'.$att->ascii_id,$params);
	}

	public function postToAttributes($r)
	{

		$att_ascii_id = Dase_Util::dirify($r->get('attribute_name'));
		//note if att_ascii_id MATCHES, we do not create a new att, we grab match
		$att = Dase_DBO_Attribute::findOrCreate($this->db,$this->collection->ascii_id,$att_ascii_id);
		$att->attribute_name = $r->get('attribute_name');
		$att->usage_notes = $r->get('usage_notes');
		$att->modifier_defined_list = $r->get('modifier_defined_list');
		if ($r->has('is_on_list_display')) {
			$att->is_on_list_display = 1;
		} else {
			$att->is_on_list_display = 0;
		}
		if ($r->has('in_basic_search')) {
			$att->in_basic_search = 1;
		} else {
			$att->in_basic_search = 0;
		}
		if ($r->has('is_public')) {
			$att->is_public = 1;
		} else {
			$att->is_public = 0;
		}
		if ($r->has('is_repeatable')) {
			$att->is_repeatable = 1;
		} else {
			$att->is_repeatable = 0;
		}
		if ($r->has('is_required')) {
			$att->is_required = 1;
		} else {
			$att->is_required = 0;
		}
		$att->html_input_type = $r->get('input_type');
		$att->update();
		$att->resort('999');
		$params['msg'] = "$att->attribute_name created";
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/attribute/'.$att->ascii_id,$params);
	}

	//todo: this belongs in Attribute Handler
	public function putAttributeDefinedValues($r)
	{
		$att = Dase_DBO_Attribute::get($this->db,$this->collection->ascii_id,$r->get('att_ascii_id'));
		$def_values = new Dase_DBO_DefinedValue($this->db);
		$def_values->attribute_id = $att->id;
		foreach ($def_values->find() as $df) {
			$df->delete();
		}
		$defined_values = trim($r->getBody());
		$pattern = "/[\n;]/";
		$munged_string = preg_replace($pattern,'%',$defined_values);
		$response = array();
		$def_value_array = explode('%',$munged_string); 
		$response['count'] = count($def_value_array);
		$response['input'] = $att->html_input_type;
		$response['defined'] = $def_value_array;
		$sort_order = 0;
		foreach ($def_value_array as $df_text) {
			if (trim($df_text)) {
				$sort_order++;
				$def_value = new Dase_DBO_DefinedValue($this->db);
				$def_value->value_text = htmlspecialchars(trim($df_text),ENT_NOQUOTES,'UTF-8');
				$def_value->attribute_id = $att->id;
				$def_value->sort_order = $sort_order;
				$def_value->insert();
			}
		}
		$r->response_mime_type = 'application/json';
		$r->renderResponse(Dase_Json::get($response));
	}

	public function getAttributeDefinedValuesJson($r)
	{
		$att = Dase_DBO_Attribute::get($this->db,$this->collection->ascii_id,$r->get('att_ascii_id'));
		$response = array();
		$def_value_array = $att->getDefinedValues(); 
		$response['count'] = count($def_value_array);
		$response['input'] = $att->html_input_type;
		$response['defined'] = $def_value_array;
		$r->response_mime_type = 'application/json';
		$r->renderResponse(Dase_Json::get($response));
	}

	public function getItemTypes($r)
	{
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('item_types',$this->collection->getItemTypes());
		$r->renderResponse($tpl->fetch('manage/item_type_form.tpl'));
	}

	public function getDeleteItems($r)
	{
		$tpl = new Dase_Template($r);
		$items = new Dase_DBO_Item($this->db);
		$items->collection_id = $this->collection->id;
		$items->status = 'delete';
		$doomed = array();
		foreach ($items->find() as $item) {
			$doomed[] = $item->getUrl($r->app_root);
		}
		$tpl->assign('collection',$this->collection);
		$tpl->assign('doomed',$doomed);
		$r->renderResponse($tpl->fetch('manage/delete_items.tpl'));

	}

	public function getItemType($r)
	{
		$coll = $this->collection->ascii_id;
		$type = Dase_DBO_ItemType::get($this->db,$this->collection->ascii_id,$r->get('type_ascii_id'));
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('type',$type);
		$tpl->assign('attributes',$this->collection->getAttributes('attribute_name'));
		$tpl->assign('item_types',$this->collection->getItemTypes());
		$tpl->assign('edit_url',$type->getUrl($coll,$r->app_root).'.atom');
		$r->set('tab','item_types');
		$r->renderResponse($tpl->fetch('manage/item_type_form.tpl'));
	}

	public function getItemTypeForm($r)
	{
		$type = new Dase_DBO_ItemType($this->db);
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('type',$type);
		$tpl->assign('item_types',$this->collection->getItemTypes());
		$r->set('tab','item_types');
		$r->renderResponse($tpl->fetch('manage/item_type_form.tpl'));
	}

	public function postToItemType($r)
	{
		$type = Dase_DBO_ItemType::get($this->db,$this->collection->ascii_id,$r->get('type_ascii_id'));
		//should redo this w/ http delete
		if ($r->has('_method') && ('delete '.$type->name == $r->get('_method'))) {
			$d = $type->name;
			$count = $type->getItemsCount();
			if ($count) {
				$params['msg'] = "sorry, but there are $count items of type $type->name so it cannot be deleted";
				$r->renderRedirect('manage/'.$this->collection->ascii_id.'/item_types',$params);
			}
			$type->expunge();
			$params['msg'] = "$d deleted";
			$r->renderRedirect('manage/'.$this->collection->ascii_id.'/item_types',$params);
		}
		$type->name = $r->get('name');
		$type->description = $r->get('description');
		$type->update();
		$params['msg'] = "$type->item_type_name updated";
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/item_type/'.$type->ascii_id,$params);
	}

	public function postToItemTypes($r)
	{

		$type_ascii_id = Dase_Util::dirify($r->get('name'));
		//note if type_ascii_id MATCHES, we do not create a new type, we grab match
		$type = Dase_DBO_ItemType::findOrCreate($this->db,$this->collection->ascii_id,$type_ascii_id);
		$type->name = $r->get('name');
		$type->description = $r->get('description');
		$type->update();
		$params['msg'] = "$type->name created";
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/item_type/'.$type->ascii_id,$params);
	}

	public function postToItemTypeAttributes($r)
	{
		$type = Dase_DBO_ItemType::get($this->db,$this->collection->ascii_id,$r->get('type_ascii_id'));
		$att = Dase_DBO_Attribute::get($this->db,$this->collection->ascii_id,$r->get('att_ascii_id'));
		$ita = new Dase_DBO_AttributeItemType($this->db);
		$ita->attribute_id = $att->id;
		$ita->item_type_id = $type->id;
		if (!$ita->findOne()) {
			$ita->insert();
		}
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/item_type/'.$type->ascii_id);
	}

	public function getItemTypeAttributesJson($r)
	{
		$type = Dase_DBO_ItemType::get($this->db,$this->collection->ascii_id,$r->get('type_ascii_id'));
		$r->renderResponse($type->getAttributesJson($this->collection->ascii_id,$r->app_root));
	}

	public function deleteItemTypeAttribute($r)
	{
		$type = Dase_DBO_ItemType::get($this->db,$this->collection->ascii_id,$r->get('type_ascii_id'));
		$att = Dase_DBO_Attribute::get($this->db,$this->collection->ascii_id,$r->get('att_ascii_id'));
		$ita = new Dase_DBO_AttributeItemType($this->db);
		$ita->attribute_id = $att->id;
		$ita->item_type_id = $type->id;
		if ($ita->findOne()) {
			$ita->delete();
			$r->renderOk('done');
		} else {
			$r->renderError(400);
		}
	}

	public function getManagers($r)
	{
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('managers',$this->collection->getManagers());
		$r->renderResponse($tpl->fetch('manage/managers.tpl'));
	}

	public function postToManagers($r)
	{
		if (!$r->has('auth_level')) {
			$params['msg'] = 'You must select an Authorization Level';
			$r->renderRedirect('manage/'.$this->collection->ascii_id.'/managers',$params);
		}
		if (!$r->has('dase_user_eid')) {
			$params['msg'] = 'You must enter an EID';
			$r->renderRedirect('manage/'.$this->collection->ascii_id.'/managers',$params);
		}

		$eid = strtolower($r->get('dase_user_eid'));

		if (!Dase_DBO_DaseUser::get($this->db,$eid)) {
			$params['msg'] = 'User '.$eid.' does not yet exist';
			$r->renderRedirect('manage/'.$this->collection->ascii_id.'/managers',$params);
		}
		$mgr = new Dase_DBO_CollectionManager($this->db);
		$mgr->dase_user_eid = $eid;
		$mgr->auth_level = $r->get('auth_level');
		$mgr->collection_ascii_id = $this->collection->ascii_id;
		$mgr->created = date(DATE_ATOM);
		$mgr->created_by_eid = $this->user->eid;
		try {
			$mgr->insert();
			$params['msg'] = 'success!';
		} catch (Exception $e) {
			$params['msg'] = 'there was a problem:'.$e->getMessage();;
		}
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/managers',$params);
	}

	public function getUploader($r)
	{
		$tpl = new Dase_Template($r);
		$tpl->assign('collection',$this->collection);
		$tpl->assign('item_types',$this->collection->getItemTypes());
		$r->renderResponse($tpl->fetch('manage/uploader.tpl'));
	}

	public function postToUploader($r)
	{
		//todo: check ppd?
		$input_name = '';
		//form can use any 'name' it wishes
		foreach ($r->_files as $k => $v) {
			$input_name = $k;
			break; //just get the first one
		}
		if ($input_name && is_file($r->_files[$input_name]['tmp_name'])) {
			$name = $r->_files[$input_name]['name'];
			$path = $r->_files[$input_name]['tmp_name'];
			$type = $r->_files[$input_name]['type'];
			if (!Dase_Media::isAcceptable($type)) {
				Dase_Log::debug(LOG_FILE,$type.' is not a supported media type');
				$r->renderError(415,'unsupported media type: '.$type);
			}
			if (!is_uploaded_file($path)) {
				$r->renderError(400,'no go upload');
			}
			Dase_Log::info(LOG_FILE,'uploading file '.$name.' type: '.$type);

			try {
					//this'll create thumbnail, viewitem, and any derivatives
					$file = Dase_File::newFile($this->db,$path,$type,$name,BASE_PATH);
			} catch(Exception $e) {
					Dase_Log::debug(LOG_FILE,'add to collection error: '.$e->getMessage());
					$r->renderError(409,$e->getMessage());
			}
		
			$item = $this->collection->createNewItem(null,$this->user->eid);
			if ($r->has('title')) {
				$item->setValue('title',$r->get('title'));
			} else {
				$item->setValue('title',$name);
			}

			try {
				$media_file = $file->addToCollection($item,true,MEDIA_DIR); //true means tets for dups
			} catch(Exception $e) {
				Dase_Log::debug(LOG_FILE,'add to collection error: '.$e->getMessage());
				$r->renderError(409,$e->getMessage());
			}
			$item->setItemType($r->get('item_type'));
			//here's where we map admin_att to real att
			$item->mapConfiguredAdminAtts();
			$item->buildSearchIndex();
		} else {
			//no file, if there is a title, assume it is a new item w/o media
			if ($r->has('title')) {
				$item = $this->collection->createNewItem(null,$this->user->eid);
				$item->setValue('title',$r->get('title'));
				$item->setItemType($r->get('item_type'));
				$item->buildSearchIndex();
			} else {
				$r->renderError(400,'could not upload file');
			}
		}
		$r->renderRedirect('manage/'.$this->collection->ascii_id.'/uploader');
	}

	public function postToIndexer($r) 
	{
		$this->collection->buildSearchIndex();
		$params['msg'] = "rebuilt indexes for $this->collection->collection_name";
		$r->renderRedirect('',$params);
	}
}

