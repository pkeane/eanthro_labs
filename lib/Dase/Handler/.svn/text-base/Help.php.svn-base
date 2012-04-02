<?php

class Dase_Handler_Help extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'help',
		'{identifier}' => 'answer',
	);

	protected function setup($r)
	{
	}

	public function getHelp($r) {
		$t = new Dase_Template($r);
		$t->assign('collection',Dase_Atom_Feed::retrieve($r->app_root.'/collection/dase_help.atom?limit=100'));
		$r->renderResponse($t->fetch('help.tpl'));
	}

	public function getAnswer($r) {

		$ident = $r->get('identifier');
		$search_result = Dase_Atom_Feed::retrieve($r->app_root.'/search.atom?c=dase_help&q=@identifier:"'.$ident.'"&limit=100');
		foreach ($search_result->entries as $e) {
			$meta = $e->getMetadata('answer');
			$r->renderResponse($meta['text']);
		}
		$r->renderError(401);
	}
}

