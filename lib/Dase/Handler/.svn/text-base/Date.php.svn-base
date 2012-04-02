<?php

/** used to synchronize client & server */
class Dase_Handler_Date extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'date',
	);

	protected function setup($r)
	{
	}

	public function getDate($r) {
		$r->renderResponse(date('Ymd',time()));
	}
}

