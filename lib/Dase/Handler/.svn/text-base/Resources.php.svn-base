<?php

class Dase_Handler_Resources extends Dase_Handler
{
	//map uri_templates to resources
	//and create parameters based on templates
	public $resource_map = array(
		'/' => 'index',
		'uploader' => 'uploader',
	);

	protected function setup($r)
	{
	}

	public function getIndex($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('resources/index.tpl'));
	}

	public function getUploader($r) 
	{
		$t = new Dase_Template($r);
		$r->response_mime_type = 'text/plain';

		$r->renderResponse(file_get_contents(BASE_PATH.'/bin/uploader.py'));
	}

}

