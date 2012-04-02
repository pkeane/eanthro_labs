<?php


class Dase_Handler_Search extends Dase_Handler
{

	public $resource_map = array(
		'/' => 'search',
		'item' => 'search_item',
		'md5' => 'search_md5',
		'{att}/{val}' => 'att_val',
		//'{collection_ascii_id}' => 'search_collection'
	);

	public function setup($r)
	{
		if ($r->getCookie('display')) {
			$r->set('display',$r->getCookie('display'));
		}

		if ($r->has('max')) {
			$this->max = $r->get('max');
		} else {
			//setting $r allows app cache-ability
			//but...breaks intermediate caching (work on that)
			if ($r->getCookie('max')) {
				//necessary???
				$r->set('max',$r->getCookie('max'));
				//for app cache:
				$r->setQueryStringParam('max',$r->getCookie('max'));
				$this->max = $r->getCookie('max');
			} else {
				$this->max = MAX_ITEMS;
			}
		}
		if ($r->has('start')) {
			$this->start = $r->get('start');
		} else {
			$this->start = 0;
		}
		if ($r->has('num')) {
			$this->num = $r->get('num');
		} else {
			$this->num = 0;
		}
		if ($r->has('sort')) {
			$this->sort = $r->get('sort');
		} else {
			$this->sort = '';
		}
		if ($r->has('uid')) {
			$this->uid = $r->get('uid');
		} else {
			$this->uid = '';
		}
	}

	public function getSearchMd5($r)
	{
		$file = new Dase_DBO_MediaFile($this->db);
		$file->md5 = $r->get('q');
		$res = "files matching $file->md5\n"; 
		foreach ($file->find() as $mf) {
			$item = new Dase_DBO_Item($this->db);
			$item->load($mf->item_id);
			$res .= $item->getUrl($r->app_root)."\n";
		}
		$r->renderResponse($res);
	}

	public function getAttVal($r)
	{
		$att = urlencode($r->get('att'));
		$val = urlencode($r->get('val'));
		$r->renderRedirect($r->app_root.'/search?q='.$att.':'.$val);
	}

	public function getSearchAtom($r)
	{
		$r->checkCache();
		$search = new Dase_Solr($this->db,$this->config);
		$search->prepareSearch($r,$this->start,$this->max,$this->num,$this->sort,$this->uid);
		$atom_feed = $search->getResultsAsAtom();
		$r->renderResponse($atom_feed);
	}

	public function getSearchTxt($r)
	{
		$this->getSearchUris($r);
	}

	public function getSearchUris($r)
	{
		$r->checkCache();
		$search = new Dase_Solr($this->db,$this->config);
		$search->prepareSearch($r,$this->start,$this->max,$this->num,$this->sort,$this->uid);
		$ids = $search->getResultsAsIds();
		$uris = '';
		foreach ($ids as $id) {
			$uris .= $app_root.'/item/'.$id."\n";
		}
		$r->renderResponse($uris);
	}

	public function getSearchJson($r)
	{
		//move OUT of controller
		$r->checkCache();
		$search = new Dase_Solr($this->db,$this->config);
		$search->prepareSearch($r,$this->start,$this->max,$this->num,$this->sort,$this->uid);
		$ids = $search->getResultsAsIds();
		$json = "{\"app_root\":\"$r->app_root\",\"start\":\"$this->start\",\"total\":\"$search->total\",\"max\":\"$this->max\",\"items\":[";
		$items = array();
		foreach ($ids as $id) {
			$docs = new Dase_DBO_ItemJson($this->db);
			$docs->unique_id = $id;
			if ($docs->findOne()) {
				$items[] = $docs->doc;
			}
		}
		$json .= join(',',$items).']}';
		$result = str_replace('{APP_ROOT}',$r->app_root,$json);

		if($r->get('callback')){
			$r->renderResponse($r->get('callback').'('.$result.');');
		}
		else{
			$r->renderResponse($result);
		}
	}

	public function getSearchItemAtom($r)
	{
		$r->checkCache();
		$search = new Dase_Solr($this->db,$this->config);
		//so we only get ONE record
		//$this->max =1;
		$search->prepareSearch($r,$this->start,$this->max,$this->num,$this->sort,$this->uid);
		$atom_feed = $search->getResultsAsItemAtom();
		$r->renderResponse($atom_feed);
	}

	public function getSearchItem($r)
	{
		$r->checkCache();
		$tpl = new Dase_Template($r);
		$feed = Dase_Atom_Feed::retrieve($r->app_root.'/'.$r->url.'&format=atom');
		if (!$feed->getOpensearchTotal()) {
			$url = str_replace('search/item?','search?',$r->url);
			$url = str_replace('uid=','x=',$url);
			$r->renderRedirect($r->app_root.'/'.$url);
		}
		$tpl->assign('item',$feed);
		$r->renderResponse($tpl->fetch('item/display.tpl'));
	}

	public function getSearch($r)
	{
		$r->checkCache();
		$tpl = new Dase_Template($r);

		//default slidehow max of 100
		$json_url = $r->app_root.'/'.$r->url.'&format=json&max=100';
		$tpl->assign('json_url',$json_url);

		$feed_url = $r->app_root.'/'.$r->url.'&format=atom';
		$tpl->assign('feed_url',$feed_url);

		$feed = Dase_Atom_Feed::retrieve($feed_url);

		//single hit goes directly to item
		$count = $feed->getCount();
		if (1 == $count) {
			//todo use preg_replace and guarentee only one replacement
			$url = str_replace('search?','search/item?',$r->url);
			$r->renderRedirect($r->app_root.'/'.$url.'&num=1');
		}
		if (0 == $count) {
			$coll = $r->get('collection_ascii_id');
			if (!$coll) {
				//won't go back to collection page unless
				//just one collection is being searched
				$coll_array = $r->get('c',true);
				if (1 == count($coll_array)) {
					$coll = $coll_array[0];
				}
			}
			if ($coll) {
				$params['msg'] = 'no items found';
				$params['failed_query'] = $feed->getQuery();
				$r->renderRedirect($r->app_root.'/collection/'.$coll,$params);
			} else {
				$params['msg'] = 'no items found';
				$params['failed_query'] = $feed->getQuery();
				$r->renderRedirect($r->app_root.'/collections',$params);
			}
		}
		$end = $this->start+$this->max;
		if ($end > $count) {
			$end = $count;
		}
		$tpl->assign('start',$this->start);
		$tpl->assign('end',$end);
		$tpl->assign('sort',$r->get('sort'));
		$tpl->assign('items',$feed);
		if ('list' == $r->get('display')) {
			$tpl->assign('display','list');
		} else {
			$tpl->assign('display','grid');
		}
		$r->renderResponse($tpl->fetch('item_set/search.tpl'));
	}
}

