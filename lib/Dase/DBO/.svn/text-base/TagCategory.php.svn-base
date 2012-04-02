<?php

require_once 'Dase/DBO/Autogen/TagCategory.php';

class Dase_DBO_TagCategory extends Dase_DBO_Autogen_TagCategory 
{

	public function getTag()
	{
		$tag = new Dase_DBO_Tag($this->db);
		$tag->load($this->tag_id);
		return $tag;
	}
}
