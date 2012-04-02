<?php

require_once 'Dase/DBO/Autogen/Value.php';

class Dase_DBO_Value extends Dase_DBO_Autogen_Value 
{
	public $attribute =  null;
	public $item =  null;

	public function getAttribute()
	{
		if (!$this->attribute) {
			$att = new Dase_DBO_Attribute($this->db);
			$att->load($this->attribute_id);
			$this->attribute = $att;
		}
		return $this->attribute;
	}

	public function getItem()
	{
		if (!$this->item) {
			$item = new Dase_DBO_Item($this->db);
			$item->load($this->item_id);
			$this->item = $item;
		}
		return $this->item;
	}
}

