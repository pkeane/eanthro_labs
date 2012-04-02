<?php
Class Dase_Solr
{
	private $coll_filters = array();
	private $max;
	private $solr_base_url;
	private $solr_update_url;
	private $solr_version = '2.2';
	private $start;
	private $request;
	public $total;
	public static $specialchars = array(
		'+','-','&&','||','!','(',')','{','}','[',']','^','"','~','*','?',':','\\'
	); //note the last one is a single backslash!


	function __construct($db,$config) 
	{
		$this->solr_base_url = $config->getSearch('solr_base_url');
		$this->solr_update_url = $this->solr_base_url.'/update';
		$this->config = $config;
		$this->db = $db; // used to scrub 
	}

	private function _cleanUpUrl($url)
	{
		$url = Dase_Util::unhtmlspecialchars($url);

		//omit start param 
		$url = preg_replace('/(\?|&|&amp;)start=[0-9]*/i','',$url);

		//omit format param 
		$url = preg_replace('/(\?|&|&amp;)format=\w*/i','',$url);

		//omit max param 
		$url = preg_replace('/(\?|&|&amp;)max=[0-9]*/i','',$url);

		//omit num param
		$url = preg_replace('/(\?|&|&amp;)num=\w*/i','',$url);

		//omit sort param
		$url = preg_replace('/(\?|&|&amp;)sort=\w*/i','',$url);

		//omit uid param
		//MAY NEED WORK ??
		$url = preg_replace('/(\?|&|&amp;)uid=[^&]*/i','',$url);

		//last param only PHP >= 5.2.3
		//$url = htmlspecialchars($url,ENT_COMPAT,'UTF-8',false);
		//beware double encoding
		$url = Dase_Util::unhtmlspecialchars($url);
		$url = htmlspecialchars($url,ENT_COMPAT,'UTF-8');
		return $url;
	}

	public function prepareSearch($request,$start,$max,$num=0,$sort='',$uid='')
	{
		$this->request = $request;
		$this->start = $start;
		$this->max = $max;
		$this->num = $num;
		$this->sort = $sort;
		$this->uid = $uid;

		if ($num) {
			//num is used to access a specific item
			//$start = $num-1;
		}

		$query_string = $request->query_string;

		$matches = array();

		//remove all collection filters
		$query_string = preg_replace('/(^|\?|&|&amp;)c=([^&]+)/i','',$query_string);
		//$query_string = preg_replace('/(^|\?|&|&amp;)collection=([^&]+)/i','',$query_string);
		$query_string = preg_replace('/(^|\?|&|&amp;)collection_ascii_id=([^&]+)/i','',$query_string);

		//remove item_type filter
		$query_string = preg_replace('/(^|\?|&|&amp;)item_type=([^&]+)/i','',$query_string);

		//get rid of type limit
		$query_string = preg_replace('/(^|\?|&|&amp;)type=([^&]+)/i','',$query_string);
		$query_string = preg_replace('/(^|\?|&|&amp;)format=([^&]+)/i','',$query_string);

		//omit start
		$query_string = preg_replace('/(\?|&|&amp;)start=\w*/i','',$query_string);

		$collection_param = '';
		$sort_param = '';
		$filter_query = '';

		//collection= trumps any c=
		$coll_filter = $request->get('collection_ascii_id');
		if ($coll_filter) {
			//$filter_query = '&fq=collection:'.urlencode('"'.$coll_filter.'"');
			$filter_query = '&fq=c:'.urlencode('"'.$coll_filter.'"');
		} else {
			$coll_filters = $request->get('c',true);
			if (count($coll_filters) && $coll_filters[0]) {
				$filter_query = '&fq=(c:'.join('+OR+c:',$coll_filters).')';
				$this->coll_filters = $coll_filters;
			}
		}

		$sort = $request->get('sort');
		if ($sort) {
			if (
				'desc' === strtolower(trim(substr($sort,-5))) ||
				'asc' === strtolower(trim(substr($sort,-4)))
			) {	
				//nothin'
			} else {
				$sort = $sort.' asc';
			}
			if (0 === strpos($sort,'_')) {
				$sort_param = '&sort='.urlencode($sort);
			} else {
				//searches on the 'exact' string (non-tokenized)
				$sort_param = '&sort=@'.urlencode($sort);
			}

			$query_string = preg_replace('/(\?|&|&amp;)sort=([^&]*)/i','',$query_string);
		} else {
			$sort_param = '&sort=_created+desc';
			$query_string = preg_replace('/(\?|&|&amp;)sort=([^&]*)/i','',$query_string);
		}

		if ($request->get('item_type') && 'none' != $request->get('item_type')) {
			$query_string = preg_replace('/(^|\?|&|&amp;)q=/i','&q=item_type:'.$request->get('item_type').'+',$query_string);
		} elseif 
			//if there is no q param, use the collection 
			//filter as q to get (all) by collection
			(!$request->get('q')) { //empty q param
				$query_string = preg_replace('/(^|\?|&|&amp;)q=([^&]*)/i','',$query_string);
				$query_string = preg_replace('/fq=/i','q=',$filter_query);
				$filter_query='';
			}

		$this->solr_search_url = 
			$this->solr_base_url
			.'/select/?'.$query_string
			.$filter_query
			.'&version='.$this->solr_version
			.'&rows='.$max
			.'&start='.$start
			.'&facet=true'
			.'&facet.field=c'
			.$collection_param
			.$sort_param;

	}

	private function _getSearchResults() 
	{
		Dase_Log::debug(LOG_FILE,'SOLR SEARCH: '.$this->solr_search_url);
		list($http_code,$res) = Dase_Http::get($this->solr_search_url,null,null);
		if ('4' == substr($http_code,0,1) || '5' == substr($http_code,0,1)) {
			Dase_Log::debug(LOG_FILE,'SOLR ERROR :'.$res);
			return '<error/>';
		}

		//view solr document itself
		if ($this->request && $this->request->get('solr')) {
			$this->request->response_mime_type = 'application/xml';
			$this->request->renderResponse($res);
		}

		return $res;
	}

	public function getResultsAsAtom() 
	{
		$app_root = $this->request->app_root;
		//use XMLReader for speed

		$total = 0;
		$coll_tallies = array();
		$ids = array();

		$reader = new XMLReader();
		if (false === $reader->XML($this->_getSearchResults())) {
			Dase_Log::debug(LOG_FILE,'SOLR ERROR : error reading search engine xml');
		}
		while ($reader->read()) {
			//get total number found
			if ($reader->localName == "result" && $reader->nodeType == XMLReader::ELEMENT) {
				$total = $reader->getAttribute('numFound');
			}
			//get entries
			if ($reader->localName == "str" && $reader->nodeType == XMLReader::ELEMENT) {
				if ('_id' == $reader->getAttribute('name')) {
					$reader->read();
					$ids[] = $reader->value;
				}
			}
			//get collection tallies
			if ($reader->localName == "lst" && $reader->nodeType == XMLReader::ELEMENT) {
				if ('c' == $reader->getAttribute('name')) {
					while ($reader->read()) {
						if ($reader->localName == "int" && $reader->nodeType == XMLReader::ELEMENT) {
							$tally['coll'] = $reader->getAttribute('name');
							@$tally['collection_name'] = $GLOBALS['app_data']['collections'][$tally['coll']];
							//advance reader
							$reader->read();
							$tally['count'] = $reader->value;
							if ($tally['count']) {
								$coll_tallies[] = $tally;
							}
							$tally = array();
						} 
					}
				}
			}
		}
		$reader->close();

		$url = $this->_cleanUpUrl($this->request->getUrl());
		$grid_url = $url.'&amp;start='.$this->start.'&amp;max='.$this->max.'&amp;display=grid&amp;sort='.$this->sort;
		$list_url = $url.'&amp;start='.$this->start.'&amp;max='.$this->max.'&amp;display=list&amp;sort='.$this->sort;

		$id = $app_root.'/search/'.md5($url);
		$updated = date(DATE_ATOM);

		//todo: probably the q param
		//note: bug -- this chops off last part of query
		//echo if it contains an ampersand
		//$query = '';
		preg_match('/(\?|&|&amp;)q=([^&]+)/i', urldecode($this->solr_search_url), $matches);
		if (isset($matches[2])) {
			$query = htmlspecialchars(urlencode($matches[2]));
		}

		if (!$total) {
			Dase_Log::info(FAILED_SEARCH_LOG,$query);
		}

		$feed = <<<EOD
<feed xmlns="http://www.w3.org/2005/Atom"
	  xmlns:thr="http://purl.org/syndication/thread/1.0">
  <author>
	<name>DASe (Digital Archive Services)</name>
	<uri>http://daseproject.org</uri>
	<email>admin@daseproject.org</email>
  </author>
  <title>DASe Search Result</title>
  <link rel="alternate" title="Search Result" href="$url" type="text/html"/>
  <link rel="related" title="grid" href="$grid_url" type="text/html"/>
  <link rel="related" title="list" href="$list_url" type="text/html"/>
  <updated>$updated</updated>
  <category term="search" scheme="http://daseproject.org/category/feedtype"/>
  <id>$id</id>
  <totalResults xmlns="http://a9.com/-/spec/opensearch/1.1/">$total</totalResults>
  <startIndex xmlns="http://a9.com/-/spec/opensearch/1.1/">$this->start</startIndex>
  <itemsPerPage xmlns="http://a9.com/-/spec/opensearch/1.1/">$this->max</itemsPerPage>
  <Query xmlns="http://a9.com/-/spec/opensearch/1.1/" role="request" searchTerms="$query"/>
EOD;

		//next link
		$next = $this->start + $this->max;
		if ($next <= $total) {
			$next_url = $url.'&amp;start='.$next.'&amp;max='.$this->max.'&amp;sort='.$this->sort;
			$feed .= "\n  <link rel=\"next\" href=\"$next_url\"/>";
		}

		//previous link
		$previous = $this->start - $this->max;
		if ($previous >= 0) {
			$previous_url = $url.'&amp;start='.$previous.'&amp;max='.$this->max.'&amp;sort='.$this->sort;
			$feed .= "\n  <link rel=\"previous\" href=\"$previous_url\"/>";
		}

		//collection fq
		//this will allow us to create search filters on page forms 
		foreach ($this->coll_filters as $c) {
			$feed .= "\n  <category term=\"$c\" scheme=\"http://daseproject.org/category/collection_filter\"/>\n";
		}

		$tallied = array();
		foreach ($coll_tallies as $tally) {
			$count = $tally['count'];
			$cname = $tally['collection_name'];
			$cname_specialchars = htmlspecialchars($cname);
			$coll = $tally['coll'];
			$encoded_query = $query.'&amp;c='.$coll;
			$feed .= "  <link rel=\"http://daseproject.org/relation/single_collection_search\" title=\"$cname_specialchars\" thr:count=\"$count\" href=\"q=$encoded_query\"/>\n";
		}

		if (1 == count($coll_tallies)) {
			$feed .= "  <link rel=\"http://daseproject.org/relation/collection\" title=\"$cname_specialchars\" thr:count=\"$count\" href=\"$app_root/collection/$coll\"/>\n";
			$feed .= "  <link rel=\"http://daseproject.org/relation/collection/attributes\" title=\"$cname_specialchars attributes\" href=\"$app_root/collection/$coll/attributes.json\"/>\n";
		}

		//this prevents a 'search/item' becoming 'search/item/item':
		$item_request_url = str_replace('search/item','search',$this->request->url);
		$item_request_url = preg_replace('/search/','search/item',$item_request_url,1);

		//omit format param 
		$item_request_url = preg_replace('/(\?|&|&amp;)format=\w*/i','',$item_request_url);

		$item_request_url = htmlspecialchars($item_request_url);

		$num = 0;
		//foreach ($entries as $entry_txt) {
		foreach ($ids as $unique_id) {
			$doc = new Dase_DBO_ItemAtom($this->db);
			$doc->unique_id = $unique_id;
			$doc->findOne();
			$num++;
			$setnum = $num + $this->start;
			//$entry = Dase_Util::unhtmlspecialchars($entry_txt);
			$entry = $doc->doc;
			$added = <<<EOD
<category term="$setnum" scheme="http://daseproject.org/category/position"/>
  <link rel="http://daseproject.org/relation/search-item" href="{$item_request_url}&amp;num={$setnum}&amp;uid={$unique_id}"/>
EOD;
			$entry = str_replace('<author>',$added."\n  <author>",$entry);
			$feed .= $entry;
		}
		$feed .= "</feed>";
		$feed = str_replace('{APP_ROOT}',$app_root,$feed);
		return $feed;
	}


	public function getSolrResponse($item_unique)
	{
		$url = $this->solr_base_url."/select/?q=_id:".$item_unique."&version=".$this->solr_version;
		list($http_code,$res) = Dase_Http::get($url,null,null);
		if ('4' == substr($http_code,0,1) || '5' == substr($http_code,0,1)) {
			Dase_Log::debug(LOG_FILE,'SOLR ERROR :'.$res);
			return '<error/>';
		}
		return $res;
	}


	public function getResultsAsIds() 
	{
		$app_root = $this->request->app_root;
		$ids = array();
		$reader = new XMLReader();
		if (false === $reader->XML($this->_getSearchResults())) {
			Dase_Log::debug(LOG_FILE,'SOLR ERROR : error reading search engine xml');
		}
		while ($reader->read()) {
			//get entries
			if ($reader->localName == "str" && $reader->nodeType == XMLReader::ELEMENT) {
				if ('_id' == $reader->getAttribute('name')) {
					$reader->read();
					$ids[] = $reader->value;
				}
			}
			if ($reader->localName == "result" && $reader->nodeType == XMLReader::ELEMENT) {
				$this->total = $reader->getAttribute('numFound');
			}
		}
		$reader->close();
		return $ids;
	}

	public function getResultsAsItemAtom() 
	{
		$app_root = $this->request->app_root;
		$dom = new DOMDocument('1.0','utf-8');

		$dom->loadXml($this->_getSearchResults());
		$url = $this->_cleanUpUrl($this->request->getUrl());

		$total = 0;
		foreach ($dom->getElementsByTagName('result') as $el) {
			if ('response' == $el->getAttribute('name')) {
				$total = $el->getAttribute('numFound');
			}
		}

		$id = $app_root.'/search/'.md5($url);
		$updated = date(DATE_ATOM);

		//todo: probably the q param
		preg_match('/.*(\?|&|&amp;)q=([^&]+)/i', urldecode($this->solr_search_url), $matches);

		//solr escaped " fix
		$query = stripslashes(htmlspecialchars($matches[2]));

		$num = $this->num;

		$previous = 0;
		$next = 0;
		if ($num < $total) {
			$next = $num + 1;
		}
		if ($num > 1) {
			$previous = $num - 1;
		}

		//todo: optimize!! no need to get results again
		$ids = $this->getResultsAsIds();
		if (!$this->uid && count($ids)) {
			$this->uid = $ids[0];
		}
		$next_uid = '';
		$prev_uid = '';
		$key = array_search($this->uid,$ids);
		if (false === $key) {
			//this would be the case when the uid points to a record
			//not actually in the search results.
			$total = 0;
		} else {
			if ($key+1 < $total) {
				$next_uid = $ids[$key+1];
			}
			if ($key > 0) {
				$prev_uid = $ids[$key-1];
			}
		}

		$feed = <<<EOD
<feed xmlns="http://www.w3.org/2005/Atom"
	  xmlns:thr="http://purl.org/syndication/thread/1.0">
  <author>
	<name>DASe (Digital Archive Services)</name>
	<uri>http://daseproject.org</uri>
	<email>admin@daseproject.org</email>
  </author>
  <title>DASe Search Result</title>
  <link rel="alternate" title="Search Result" href="$url" type="text/html"/>
  <updated>$updated</updated>
  <category term="searchitem" scheme="http://daseproject.org/category/feedtype"/>
  <id>$id</id>
  <totalResults xmlns="http://a9.com/-/spec/opensearch/1.1/">$total</totalResults>
  <startIndex xmlns="http://a9.com/-/spec/opensearch/1.1/">$this->start</startIndex>
  <itemsPerPage xmlns="http://a9.com/-/spec/opensearch/1.1/">$this->max</itemsPerPage>
  <Query xmlns="http://a9.com/-/spec/opensearch/1.1/" role="request" searchTerms="$query"/>
EOD;


		//omit format param 
		$item_request_url = preg_replace('/(\?|&|&amp;)format=\w+/i','',$this->request->url);
		//omit num param 
		$item_request_url = preg_replace('/(\?|&|&amp;)num=\w+/i','',$item_request_url);
		//omit uid param 
		$item_request_url = preg_replace('/(\?|&|&amp;)uid=[^&]*/i','',$item_request_url);
		$item_request_url = htmlspecialchars($item_request_url);

		if ($next_uid) {
			$next_url = $item_request_url.'&amp;num='.$next.'&amp;uid='.$next_uid;
			$feed .= "\n  <link rel=\"next\" href=\"$next_url\"/>";
		}

		if ($prev_uid) {
			$previous_url = $item_request_url.'&amp;num='.$previous.'&amp;uid='.$prev_uid;
			$feed .= "\n  <link rel=\"previous\" href=\"$previous_url\"/>";
		}

		$search_request_url = str_replace('search/item','search',$this->request->url);
		//omit format param 
		$search_request_url = preg_replace('/(\?|&|&amp;)format=\w+/i','',$search_request_url);
		//omit num param 
		$search_request_url = preg_replace('/(\?|&|&amp;)num=\w+/i','',$search_request_url);
		//omit uid param 
		$search_request_url = preg_replace('/(\?|&|&amp;)uid=[^&]*/i','',$search_request_url);
		$search_request_url = htmlspecialchars($search_request_url);

		if ($total) {
			$doc = new Dase_DBO_ItemAtom($this->db);
			$doc->unique_id = $this->uid;
			$doc->findOne();
			$entry = $doc->doc;
			$added = <<<EOD
  <link rel="up" href="{$search_request_url}"/>
  <category term="$num" scheme="http://daseproject.org/category/position"/>
EOD;
			$entry = str_replace('<author>',$added."\n  <author>",$entry);
			$feed .= $entry;
		}
		$feed .= "</feed>";
		$feed = str_replace('{APP_ROOT}',$app_root,$feed);
		return $feed;
	}

	public function buildItemIndex($item,$commit=true)
	{
		return $this->postToSolr($item,$commit);
	}	

	public function buildItemSetIndex($item_array)
	{
		foreach ($item_array as $item) {
			$this->postToSolr($item,false);
		}
		return $this->commit();
	}	

	public function deleteItemIndex($item)
	{
		$start = Dase_Util::getTime();
		$delete_doc = '<delete><id>'.$item->getUnique().'</id></delete>';
		$resp = Dase_Http::post($this->solr_update_url,$delete_doc,null,null,'text/xml');
		Dase_Http::post($this->solr_update_url,'<commit/>',null,null,'text/xml');
		$end = Dase_Util::getTime();
		$index_elapsed = round($end - $start,4);
		return $resp.' deleted '.$item->serial_number.' index: '.$index_elapsed;
	}	

	public function getLatestTimestamp($coll) 
	{
		$url = $this->solr_base_url."/select/?q=c:".$coll."&start=0&max=1&sort=timestamp+desc&version=".$this->solr_version;
		Dase_Log::debug(LOG_FILE,'query SOLR: '.$url);
		$res = file_get_contents($url);
		$dom = new DOMDocument('1.0','utf-8');
		$dom->loadXml($res);
		foreach ($dom->getElementsByTagName('date') as $el) {
			if ('timestamp' == $el->getAttribute('name')) {
				return $el->nodeValue;
			}
		}
	}

	public function buildItemSolrDoc($item,$wrap_in_add_tag=true)
	{
		//used to create an xml doc
		$dom = new DOMDocument();

		if ($wrap_in_add_tag) {
			$root_el = $dom->createElement('add');
			$root = $dom->appendChild($root_el);
			$doc_el = $dom->createElement('doc');
			$doc = $root->appendChild($doc_el);
		} else {
			$root_el = $dom->createElement('doc');
			$root = $dom->appendChild($root_el);
			$doc = $root;
		}
		$id = $doc->appendChild($dom->createElement('field'));
		$id->appendChild($dom->createTextNode($item->p_collection_ascii_id.'/'.$item->serial_number));
		$id->setAttribute('name','_id');

		$updated = $doc->appendChild($dom->createElement('field'));
		$updated->appendChild($dom->createTextNode($item->created));
		$updated->setAttribute('name','_created');

		$updated = $doc->appendChild($dom->createElement('field'));
		$updated->appendChild($dom->createTextNode($item->updated));
		$updated->setAttribute('name','_updated');

		$item_id = $doc->appendChild($dom->createElement('field'));
		$item_id->appendChild($dom->createTextNode($item->id));
		$item_id->setAttribute('name','_item_id');

		$serial_number = $doc->appendChild($dom->createElement('field'));
		$serial_number->appendChild($dom->createTextNode($item->serial_number));
		$serial_number->setAttribute('name','_serial_number');

		$c = $doc->appendChild($dom->createElement('field'));
		$c->appendChild($dom->createTextNode($item->p_collection_ascii_id));
		$c->setAttribute('name','c');

		$coll = $doc->appendChild($dom->createElement('field'));
		$coll->appendChild($dom->createTextNode($item->collection_name));
		$coll->setAttribute('name','collection');

		$media_count = $doc->appendChild($dom->createElement('field'));
		$media_count->appendChild($dom->createTextNode($item->getMediaCount()));
		$media_count->setAttribute('name','_media_count');

		$it = $doc->appendChild($dom->createElement('field'));
		$it->appendChild($dom->createTextNode($item->item_type_ascii_id));
		$it->setAttribute('name','item_type');

		$it_name = $doc->appendChild($dom->createElement('field'));
		$it_name->appendChild($dom->createTextNode($item->item_type_name));
		$it_name->setAttribute('name','item_type_name');

		$search_text = array();
		$admin_search_text = array();

		$search_text[] = $item->id;
		$search_text[] = $item->serial_number;

		foreach ($item->getMetadata(true) as $meta) {
			if (0 == $meta['collection_id']) {
				//admin metadata 
			} else {
				$value_set = array();
				$value_set['text'] = $meta['value_text'];
				if ($meta['url']) {
					$value_set['url'] = $meta['url'];
				}
				if ($meta['edit-id']) {
					$value_set['edit'] = $meta['edit-id'];
				}
			}
			//create "bags" for search text & admin text
			if (0 === strpos($meta['ascii_id'],'admin_')) {
				$admin_search_text[] = $meta['value_text'];
			} else {
				if ($meta['in_basic_search']) {
					$search_text[] = $meta['value_text'];
				}
			}
			if ($meta['url']) {
				$search_text[] = $meta['url'];
			}
			//allows filtering on mod (e.g. role:painter)
			if ($meta['modifier']) {
				$search_text[] = $meta['modifier'];
				if ($meta['modifier_type']) {
					$field = $doc->appendChild($dom->createElement('field'));
					$field->appendChild($dom->createTextNode($meta['modifier']));
					$field->setAttribute('name',$meta['modifier_type']);
				}
			}
			//allows fielded search:
			$field = $doc->appendChild($dom->createElement('field'));
			$field->appendChild($dom->createTextNode($meta['value_text']));
			//attribute ascii_ids
			$field->setAttribute('name',$meta['ascii_id']);

			$field = $doc->appendChild($dom->createElement('field'));
			$field->appendChild($dom->createTextNode($meta['value_text']));
			$field->setAttribute('name',$meta['attribute_name']);

			//allows fielded exact search -- precede att_ascii_id w/ @:
			$field = $doc->appendChild($dom->createElement('field'));
			$field->appendChild($dom->createTextNode($meta['value_text']));
			//attribute ascii_ids
			$field->setAttribute('name','@'.$meta['ascii_id']);
		}
		$field = $doc->appendChild($dom->createElement('field'));
		$field->appendChild($dom->createTextNode(join(" ",$search_text)));
		$field->setAttribute('name','_search_text');

		if (count($admin_search_text)) {
			$field = $doc->appendChild($dom->createElement('field'));
			$field->appendChild($dom->createTextNode(join("\n",$admin_search_text)));
			$field->setAttribute('name','admin');
		}
		$dom->formatOutput = true;
		return $dom->saveXML();
	}

	public function deleteCollectionIndexes($coll)
	{
		$start = Dase_Util::getTime();
		$delete_doc = '<delete><query>c:'.$coll.'</query></delete>';
		$resp = Dase_Http::post($this->solr_update_url,$delete_doc,null,null,'text/xml');
		Dase_Http::post($this->solr_update_url,'<commit/>',null,null,'text/xml');
		$end = Dase_Util::getTime();
		$index_elapsed = round($end - $start,4);
		return $resp.' deleted '.$coll.' index: '.$index_elapsed;
	}

	public function commit()
	{
		return Dase_Http::post($this->solr_update_url,'<commit/>',null,null,'text/xml');
	}

	//todo: make autocommit a config option
	public function postToSolr($item,$commit=true)
	{
		$start_check = Dase_Util::getTime();
		$start_get_doc = Dase_Util::getTime();
		$check_elapsed = round($start_get_doc - $start_check,4);
		Dase_Log::debug(LOG_FILE,'post to SOLR: '.$this->solr_update_url.' item '.$item->getUnique());
		$solr_doc = $this->buildItemSolrDoc($item);
		//return $solr_doc;
		$start_index = Dase_Util::getTime();
		$get_doc_elapsed = round($start_index - $start_get_doc,4);
		$resp = Dase_Http::post($this->solr_update_url,$solr_doc,null,null,'text/xml');
		if ($commit) {
			Dase_Http::post($this->solr_update_url,'<commit/>',null,null,'text/xml');
		}
		$end = Dase_Util::getTime();
		$index_elapsed = round($end - $start_index,4);
		return $resp.' check: '.$check_elapsed.' get_doc: '.$get_doc_elapsed.' index: '.$index_elapsed;
	}
}


