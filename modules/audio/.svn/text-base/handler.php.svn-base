<?php

class Dase_ModuleHandler_Audio extends Dase_Handler {

	public $resource_map = array(
		'/' => 'list',
		'{coll_ascii}' => 'list',
	);

	public function setup($r)
	{
			$r->getUser();
	}

	public function getIndex($r) 
	{
		$r->renderResponse('hello index');
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
		$url = 'https://dase.laits.utexas.edu/search.json?q=keyword:list&max=99&sort=title&c='.$c->ascii_id;
		$json = file_get_contents($url);
		$php_data = json_decode($json,true);
		$t->assign('c',$c);
		$t->assign('items',$php_data['items']);
		$r->renderResponse($t->fetch('index.tpl'));
	}
}
