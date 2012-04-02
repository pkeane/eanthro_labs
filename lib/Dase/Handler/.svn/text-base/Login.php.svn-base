<?php

class Dase_Handler_Login extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'login',
		'form' => 'login_form',
		'{eid}' => 'login',
	);

	protected function setup($r)
	{
	}

	public function getLogin($r)
	{
		$t = new Dase_Template($r);
		//'target' is the page to redirect to after login is complete
		$t->assign('target',$r->get('target'));
		$r->renderResponse($t->fetch('login_form.tpl'));
	}

	public function getLoginForm($r)
	{
		//target is empty in the most common case
		//but it *is* used to get people back to
		//their place if they are prompted in the middle of things
		$t = new Dase_Template($r);
		$t->assign('target',$r->get('target'));
		$r->renderResponse($t->fetch('login_form.tpl'));
	}

	public function postToLogin($r)
	{
		//this is the default, uber-simple login
		//which should be overidden by a module
		//all valid users need to be superusers
		$username = strtolower($r->get('username'));
		$pass = $r->get('password');
		$superusers = $r->getSuperusers();
		//todo: this is why params are not saved -- super ONLY
		if (isset($superusers[$username]) && $superusers[$username] == $pass) {
			$user = new Dase_DBO_DaseUser($this->db);
			$user->retrieveByEid($username);
			$user->initCart();
			$r->setCookie('collections',Dase_Json::get(Dase_DBO_Collection::getLookupArray($this->db)));
			$r->setCookie('eid',$username);
			$r->setCookie('max',$user->max_items);
			$r->setCookie('display',$user->display);
			$r->renderRedirect(urldecode($r->get('target')));
		} else {
			//I could probably just display here instead of redirect
			$params['msg'] = 'incorrect username/password';
			$r->renderRedirect("login/form",$params);
		}
	}

	/**
	 * this method will be called
	 * w/ an http delete to '/login' *or* '/login/{eid}'
	 *
	 */
	public function deleteLogin($r)
	{
		$r->clearCookies();
	//	$r->renderRedirect('login/form');
		$logout_url = $r->app_root . '/logini/form';
		$json = Dase_Json::get(array('location' => $logout_url));
		$r->response_mime_type = 'application/json';
		$r->renderResponse($json);
	}
}

