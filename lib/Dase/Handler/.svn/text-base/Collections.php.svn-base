<?php

class Dase_Handler_Collections extends Dase_Handler
{
	//map uri_templates to resources
	//and create parameters based on templates
	public $resource_map = array(
		'/' => 'collections',
		'acl' => 'acl',
		"pk/test" => 'test',
	);

	protected function setup($r)
	{
	}

	public function postToCollections($r) 
	{
		$user = $r->getUser('http');
		if (!$user->is_superuser) {
			$r->renderError(401,$user->eid.' is not permitted to create a collection');
		}
		$content_type = $r->getContentType();
		if ('application/atom+xml;type=entry' == $content_type ||
			'application/atom+xml' == $content_type
		) {
			$raw_input = $r->getBody();
			$client_md5 = $r->getHeader('Content-MD5');
			if ($client_md5 && md5($raw_input) != $client_md5) {
				//todo: fix this
				//$r->renderError(412,'md5 does not match');
			}
			try {
				$coll_entry = Dase_Atom_Entry::load($raw_input);
			} catch(Exception $e) {
				Dase_Log::debug(LOG_FILE,'colls handler error: '.$e->getMessage());
				$r->renderError(400,'bad xml');
			}
			if ('collection' != $coll_entry->entrytype) {
				$r->renderError(400,'must be a collection entry');
			}
			if ( $r->slug ) {
				$r->set('ascii_id',Dase_Util::dirify($r->slug));
			}
			$ascii_id = $coll_entry->create($this->db,$r);
			$user->expireDataCache($r->getCache());
			header("HTTP/1.1 201 Created");
			header("Content-Type: application/atom+xml;type=entry;charset='utf-8'");
			header("Location: ".$r->app_root."/collection/".$ascii_id.'.atom');
			echo Dase_DBO_Collection::get($this->db,$ascii_id)->asAtomEntry($r->app_root);
			exit;
		} else {
			$r->renderError(415,'cannoot accept '.$content_type);
		}
	}

	public function getCollectionsJson($r) 
	{
		if ($r->get('get_all')) {
			$public_only = false;
		} else {
			$public_only = true;
		}
		$r->renderResponse(Dase_DBO_Collection::listAsJson($this->db,$public_only,$r->app_root));
	}

	public function getCollectionsAtom($r) 
	{
		if ($r->get('get_all')) {
			$public_only = false;
		} else {
			$public_only = true;
		}
		$r->renderResponse(Dase_DBO_Collection::listAsAtom($this->db,$r->app_root,$public_only));
	}

	public function getCollections($r) 
	{
		$user = $r->getUser();
		//if no collections, redirect to archive admin screen
		//will force login screen for non-superusers if no collections
		$c = new Dase_DBO_Collection($this->db);
		if (!$c->findCount() && $user && $user->is_superuser) {
			$r->renderRedirect('admin');
		}
		$tpl = new Dase_Template($r);
		//$feed = Dase_Atom_Feed::retrieve($r->app_root.'/collections.atom');
		//$tpl->assign('collections',$feed);
		$res = Dase_Http::get($r->app_root.'/collections.json');
		$collections = Dase_Json::toPhp($res[1]);
		$tpl->assign('collections',$collections);
		$r->renderResponse($tpl->fetch('collection/list.tpl'));
	}
}

