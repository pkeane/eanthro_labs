<?php

require_once 'Dase/DBO/Autogen/DataSet.php';

class Dase_DBO_DataSet extends Dase_DBO_Autogen_DataSet 
{
		public $person_datas = array();

		public function getPersonData()
		{
				$pd = new Dase_DBO_PersonData($this->db);
				$pd->data_set_id = $this->id;
				$pd->orderBy('created DESC');
				$this->person_datas = $pd->findAll(1);
				return $this->person_datas;
		}

		public function expunge()
		{
				foreach ($this->getPersonData() as $pd) {
						$pd->delete();
				}
				$this->delete();
		}
}
