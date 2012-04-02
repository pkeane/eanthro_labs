<?php

Class Dase_Remote_User extends Dase_User
{
	/*
	 * this class is designed to allow a remote dase site
	 * (a site that simply uses dase services) to use
	 * EID-based auth
	 */ 

	function __construct($db) 
	{
		$this->config = $db->config;
	}

	public function retrieveByEid($eid)
	{
		$this->eid = $eid;
		return $this;
	}

	public function setHttpPassword($token)
	{
		$this->http_password = substr(md5($token.$this->eid.'httpbasic'),0,12);
		return $this->http_password;
	}

	public function getHttpPassword($token=null)
	{
		if (!$token) {
			if ($this->http_password) {
				//would have been set by request
				return $this->http_password;
			}
			throw new Dase_Exception('user auth is not set');
		}
		if (!$this->http_password) {
			$this->http_password = $this->setHttpPassword($token);
		}
		return $this->http_password;
	}

	public function isSuperuser($superusers)
	{
		if (in_array($this->eid,array_keys($superusers))) {
			$this->is_superuser = true;
			return true;
		}
		return false;
	}

	public function isManager($collection_ascii_id)
	{
		$url = $this->config->getAppSetting('remote_url').'/manager/'.$collection_ascii_id.'/'.$this->eid.'.json';
		$res = Dase_Http::get($url);
		if ('200' == $res[0]) {
			$data = Dase_Json::toPhp($res[1]);
			if (isset($data['auth_level'])) {
				return $data['auth_level'];
			}
		}
		return false;
	}
}


