<?php

class Dase_Handler_Manager extends Dase_Handler
{
	public $resource_map = array(
		'{collection_ascii_id}/{eid}' => 'manager',
	);

	protected function setup($r)
	{ 
	}

	public function getManagerAtom($r) 
	{
		$coll = $r->get('collection_ascii_id');
		$eid = $r->get('eid');
		$cm = Dase_DBO_CollectionManager::get($this->db,$coll,$eid);
		if ($cm) {
			$r->renderResponse($cm->asAtom($r->app_root)->asXml());
		} else {
			$r->renderError(404);
		}
	}

	public function getManagerJson($r) 
	{
		$coll = $r->get('collection_ascii_id');
		$eid = $r->get('eid');
		$cm = Dase_DBO_CollectionManager::get($this->db,$coll,$eid);
		$result = array();
		if ($cm) {
			$result['eid'] = $eid;
			$result['collection_ascii_id'] = $coll;
			$result['auth_level'] = $cm->auth_level;
			$r->renderResponse(Dase_Json::get($result));
		} else {
			$r->renderError(404);
		}
	}
}

