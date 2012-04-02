<?php

class Dase_Handler_Dataset extends Dase_Handler
{
		public $resource_map = array(
				'{id}' => 'dataset',
				'{id}/published' => 'published',
				'{id}/csv' => 'dataset_csv_form',
				'{id}/person_data/{pd_id}' => 'person_data',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function deleteDataset($r) 
		{
				$ds = new Dase_DBO_DataSet($this->db);
				if ($ds->load($r->get('id'))) {
						if ($this->user->eid != $ds->created_by) {
								$r->renderError(401);
						}
						$ds->expunge();
						$r->renderResponse('success');
				}
		}

		public function postToPublished($r) 
		{
				$ds = new Dase_DBO_DataSet($this->db);
				if (!$ds->load($r->get('id'))) {
						$r->renderError(404);
				}
				if ($this->user->eid != $ds->created_by) {
						$r->renderError(401);
				}
				$ds->is_published = $r->get('state');
				$ds->update();
				$r->renderRedirect('trackways/data_set/'.$ds->id);
		}

		public function postToDataset($r) 
		{
				$ds = new Dase_DBO_DataSet($this->db);
				if ($ds->load($r->get('id'))) {
						if ($this->user->eid != $ds->created_by) {
								$r->renderError(401);
						}
						$pd = new Dase_DBO_PersonData($this->db);
						$pd->data_set_id = $ds->id;
						$pd->foot_length = $r->get('foot_length');
						$pd->age = $r->get('age');
						$pd->gender = $r->get('gender');
						$pd->stride_length = $r->get('stride_length');
						$pd->height = $r->get('height');
						$pd->created_by = $this->user->eid;
						$pd->created = date(DATE_ATOM);
						$pd->insert();
				}
				$r->renderRedirect('trackways/data_set/'.$ds->id);
		}

		public function getDatasetCsvForm($r) 
		{
				$ds = new Dase_DBO_DataSet($this->db);
				if (!$ds->load($r->get('id'))) {
						$r->renderError(404);
				}
				if ($this->user->eid != $ds->created_by) {
						$r->renderError(401);
				}
				$t = new Dase_Template($r);
				$t->assign('dataset',$ds);
				$r->renderResponse($t->fetch('dataset_csv_form.tpl'));
		}

		public function postToDatasetCsvForm($r) 
		{
				$ds = new Dase_DBO_DataSet($this->db);
				if (!$ds->load($r->get('id'))) {
						$r->renderError(404);
				}
				if ($this->user->eid != $ds->created_by) {
						$r->renderError(401);
				}
				$file = $r->_files['uploaded_file'];
				if ($file && is_file($file['tmp_name'])) {
						$path = $file['tmp_name'];
						$row = 1;
						if (($handle = fopen($path, "r")) !== FALSE) {
								while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
										if (
												count($data) >= 5  &&
												is_numeric($data[1]) &&
												is_numeric($data[2]) &&
												is_numeric($data[3]) 
										) {
												$pd = new Dase_DBO_PersonData($this->db);
												$pd->data_set_id = $ds->id;
												$pd->gender = $data[0]; 
												$pd->age =  $data[1];
												$pd->height =  $data[2];
												$pd->foot_length =  $data[3];
												$pd->stride_length =  $data[4];
												$pd->created_by = $this->user->eid;
												$pd->created = date(DATE_ATOM);
												$pd->insert();
										}
								}
								fclose($handle);
						}
				}
				$r->renderRedirect('trackways/data_set/'.$ds->id);
		}

		public function deletePersonData($r) 
		{
				$ds = new Dase_DBO_DataSet($this->db);
				if ($ds->load($r->get('id'))) {
						if ($this->user->eid != $ds->created_by) {
								$r->renderError(401);
						}
						$pd = new Dase_DBO_PersonData($this->db);
						if ($pd->load($r->get('pd_id'))) {
								$pd->delete();
								$r->renderResponse('success');
						}
				}
		}
}

