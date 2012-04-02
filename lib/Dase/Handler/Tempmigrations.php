<?php
class Dase_Handler_Tempmigrations extends Dase_Handler {
	public $resource_map = array(
		'/' => 'tempmigrations',
		'timemap' => 'timemap',
	);

	protected function setup($r) {
		$this->user = $r->getUser();
		$r->set('footer',Dase_DBO_Itemset::getByName($this->db,$r,'footer'));
	}

	public function getTempmigrations($r) {
		$t = new Dase_Template($r);
		$t->assign('page_content',Dase_DBO_Item::getByName($this->db,$r,'temperate_migrations_banner.jpg'));
		$r->renderResponse($t->fetch('tempmigrations.tpl'));
	}

	public function getTimemap($r) {
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('tempmigrations_timemap.tpl'));
	}
}
