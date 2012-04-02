<?php

class Dase_Handler_Test extends Dase_Handler
{

	public $resource_map = array( 
		'/' => 'index',
		'auth' => 'require_auth',
		'cache' => 'cache',
	);

	protected function setup($r)
	{
	}	

	public function initTemplate($t)
	{
		$t->assign('init','initialized!');
	}

	public function getIndex($r) 
	{
		$t = new Dase_Template($r);
		$t->init($this);
		$r->renderResponse($t->fetch('test/index.tpl'));
	}

	public function getCache($r) 
	{
		$tpl = new Dase_Template($r);
		$cache = Dase_Cache::get($this->config);
		$cache->setData('my_cache_file','hello world cached');
		$data = $cache->getData('my_cache_file');
		$ip = $_SERVER['SERVER_ADDR'];
		$r->renderResponse('cached data: '.$data." ($ip)");
	}


}

