<?php

class Dase_ModuleHandler_Local extends Dase_Handler {

	public $resource_map = array(
		'/' => 'index',
		'index' => 'index',
		'{coll_ascii}' => 'list',
		'{coll_ascii}/lightbox' => 'lightbox',
		'item/{coll_ascii}/{serial_number}' => 'item',
	);

	public function setup($r)
	{
	//		$r->getUser();
	}

	public function getIndex($r) 
	{
		$r->renderResponse('hello index');
	}

	public function getItem($r) 
	{
		$t = new Dase_Template($r,true);
		$item = Dase_DBO_Item::get($this->db,$r->get('coll_ascii'),$r->get('serial_number'));
		$json = $item->asJson($r->app_root);
		$php_data = json_decode($json,true);
		$t->assign('item',$php_data);
		$t->assign('coll_ascii',$r->get('coll_ascii'));
		$r->renderResponse($t->fetch('item.tpl'));
	}

	public function getList($r) 
	{
		$t = new Dase_Template($r,true);
		$c = Dase_DBO_Collection::get($this->db,$r->get('coll_ascii'));
		if (!$c) {
				$r->renderError('404');
		}
		if ('public' != $c->visibility) {
				$r->renderError('401');
		}
		$url = 'https://dase.laits.utexas.edu/collection/'.$c->ascii_id.'/items.json';
		$json = file_get_contents($url);
		$php_data = json_decode($json,true);
		$t->assign('c',$c);
		$t->assign('items',$php_data['items']);
		$r->renderResponse($t->fetch('index.tpl'));
	}
}
