<?php

class Dase_Handler_Trackways extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'trackways',
		'instructions' => 'instructions',
		'data_sets' => 'data_sets',
		'data_set/{id}' => 'data_set',
		'about_the_math' => 'about_the_math',
		'share_photos' => 'share_photos',
		'graph_data' => 'graph_data',
		'graph_data/{eid}' => 'graph_data',
	);

	protected function setup($r)
	{
		//$this->user = $r->getUser();
		$r->set('footer',Dase_DBO_Itemset::getByName($this->db,$r,'footer'));
	}

	public function getTest($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('trackways_test.tpl'));
	}

	public function getTrackways($r) 
	{
		$t = new Dase_Template($r);
		$t->assign('front',Dase_DBO_Item::get($this->db,$r,58));
		$t->assign('page','lab_home');
		$r->renderResponse($t->fetch('trackways.tpl'));
	}

	public function getInstructions($r) 
	{
		$t = new Dase_Template($r);
		$t->assign('instr',Dase_DBO_Item::getByName($this->db,$r,'trackways_instructions'));
		$t->assign('page','instructions');
		$t->assign('steps',Dase_DBO_Itemset::getByName($this->db,$r,'trackways_steps'));
		$t->assign('thumbs',Dase_DBO_Itemset::getByName($this->db,$r,'trackways_steps_thumbs'));
		//print_r(Dase_DBO_Itemset::getByName($this->db,$r,'trackways_steps_thumbs'));exit;
		$r->renderResponse($t->fetch('trackways_instructions.tpl'));
	}

	public function getGraphData($r) 
	{
		$t = new Dase_Template($r);
		if ($r->get('eid')) {
				$t->assign('eid_filter',$r->get('eid'));
		} else {
				$t->assign('eid_filter','all');
		}

		$t->assign('time',time());
		$t->assign('page','graph_data');
		$t->assign('page_content',Dase_DBO_Item::getByName($this->db,$r,'trackways_graph_data'));
		$r->renderResponse($t->fetch('trackways_graph_data.tpl'));
	}

	public function getAboutTheMath($r) 
	{
		$t = new Dase_Template($r);
		$t->assign('math',Dase_DBO_Item::getByName($this->db,$r,'trackways_about_the_math'));
		$t->assign('page','about_the_math');
		$t->assign('concepts',Dase_DBO_Itemset::getByName($this->db,$r,'about_math'));
		$t->assign('thumbs',Dase_DBO_Itemset::getByName($this->db,$r,'about_math_thumbs'));
		//print_r(Dase_DBO_Itemset::getByName($this->db,$r,'about_math'));exit;
		$r->renderResponse($t->fetch('trackways_about_the_math.tpl'));
	}

	public function getSharePhotos($r) 
	{
		$t = new Dase_Template($r);
		$t->assign('content',Dase_DBO_Item::getByName($this->db,$r,'trackways_share_photos'));
		$photos = new Dase_DBO_Photo($this->db);
		$photos->orderBy('created DESC');
		$t->assign('photos',$photos->findAll(1));
		$t->assign('page','share_photos');
		$r->renderResponse($t->fetch('trackways_share_photos.tpl'));
	}

	public function getDataSets($r) 
	{
		$this->user = $r->getUser();
		$t = new Dase_Template($r);
		$t->assign('page','data_sets');
		$t->assign('page_content',Dase_DBO_Item::getByName($this->db,$r,'trackways_data_sets'));
		$ds = new Dase_DBO_DataSet($this->db);
		$ds->created_by = $this->user->eid;
		$t->assign('data_sets',$ds->findAll(1));
		$r->renderResponse($t->fetch('trackways_data_sets.tpl'));
	}

	public function getDataSet($r) 
	{
		$this->user = $r->getUser();
		$t = new Dase_Template($r);
		$t->assign('page','data_sets');
		$ds = new Dase_DBO_DataSet($this->db);
		if ($ds->load($r->get('id'))) {
				$ds->getPersonData();
				$t->assign('data_set',$ds);
				$r->renderResponse($t->fetch('trackways_data_set.tpl'));
		} else {
				$r->renderRedirect('trackways/data_sets');
		}
	}
}

