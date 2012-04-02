<?php

class Dase_Handler_Tools extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'demo',
		'demo' => 'demo',
		'doc' => 'doc',
		'cd' => 'cache_deleter',
		'htmlbuilder' => 'htmlbuilder',
	);

	protected function setup($r)
	{
	}

	public function getDemo($r)
	{
		$user = $r->getUser();
		$t = new Dase_Template($r);
		if ($r->has('url')) {
			$entry = Dase_Atom_Entry::retrieve($r->get('url'),$user->eid,$user->getHttpPassword());
			$t->assign('url',$r->get('url'));
			$t->assign('entry',$entry);
			$t->assign('atom_doc',$entry->asXml($entry->root));
		} else {
			$t->assign('url',$r->app_root."/service");
		}	
		$r->renderResponse($t->fetch('tools/demo.tpl'));
	}

	/** this handler method should be the target of a web hook */
	public function postToCacheDeleter($r)
	{
		$num = $r->getCache()->expunge();
		$r->renderResponse('cache deleted '.$num.' files removed');
	}

	public function getHtmlbuilder($r)
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('tools/htmlbuilder.tpl'));
	}

	public function getDocAtom($r)
	{
		$r->checkCache();
		$url = 'http://quickdraw.laits.utexas.edu/dase1/media/keanepj/text/000598739.txt';
		//$url = 'http://harpo.laits.utexas.edu:5984/test/1c919eeb2a40d850ec57b47334ba4f5c/study_set.atom';
		//$url = 'http://harpo.laits.utexas.edu:1978/one';
		$r->renderResponse(Dase_Http::get($url,'xxxx','xxx'));
	}
}
