<?php

class Dase_Remote_Search
{
	private $root;
	private $params = array();

	public function __construct($config,$coll_ascii_id ='') 
	{
		$this->root = $config->getAppSettings('remote_url');
		$this->path = 'search.json';
		if ($coll_ascii_id) {
			$this->setCollection($coll_ascii_id);
		}
	}

	/* allows configured url to be overridden */
	public function setRoot($root)
	{
		$this->root = $root;
	}

	public function setCollection($str)
	{
		$this->set('c',$str);
	}

	public function addQuery($str) {
		$this->set('q',$str);
	}

	public function setItemType($str) {
		$this->set('item_type',$str);
	}

	public function set($key,$val) 
	{
		$this->params[$key] = $val;
	}

	public function getParam($key) 
	{
		if (isset($this->params[$key])) {
			return $this->params[$key];
		}	
	}

	public function getUrl()
	{
		$url = trim($this->root,'/').'/';
		$url .= $this->path;
		$url .= '?';
		foreach ($this->params as $k => $v) {
			$url .= '&'.urlencode($k).'='.urlencode($v);
		}
		return $url;
	}

	public function getAttVals($att_ascii)
	{
		$url = $this->root.'/attribute/'.$this->getParam('c').'/'.$att_ascii.'/values.json';
		$res = Dase_Http::get($url);
		return Dase_Json::toPhp($res[1]);

	}

	public function get()
	{
		$url = $this->getUrl();
		$res = Dase_Http::get($url);
		return Dase_Json::toPhp($res[1]);
	}

}

