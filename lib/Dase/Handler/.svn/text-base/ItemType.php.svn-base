<?php

class Dase_Handler_ItemType extends Dase_Handler
{
	public $type;
	public $resource_map = array(
		'{collection_ascii_id}/{item_type_ascii_id}' => 'item_type',
		'{collection_ascii_id}/{item_type_ascii_id}/attributes' => 'attributes',
	);

	protected function setup($r)
	{
		$this->type = Dase_DBO_ItemType::get($this->db,$r->get('collection_ascii_id'),$r->get('item_type_ascii_id'));
		if (!$this->type) {
			$r->renderError(404);
		}
	}

	public function getItemType($r)
	{
		$r->renderResponse($this->type->name);
	}

	public function getItemTypeAtom($r)
	{
		$r->renderResponse($this->type->asAtomEntry($r->get('collection_ascii_id'),$r->app_root));
	}

	public function getAttributesAtom($r)
	{
		$r->renderResponse($this->type->getAttributesFeed($r->get('collection_ascii_id'),$r->app_root));
	}

	public function getAttributesJson($r)
	{
		$r->renderResponse($this->type->getAttributesJson($r->get('collection_ascii_id'),$r->app_root));
	}

}
