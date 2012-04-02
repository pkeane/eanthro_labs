<?php

class Dase_Handler_Datasets extends Dase_Handler
{
	public $resource_map = array(
			'{eid}' => 'datasets',
			'foot_length/all' => 'foot_length',
			'foot_length/{eid}' => 'foot_length',
			'stride_length/all' => 'stride_length',
			'stride_length/{eid}' => 'stride_length',
			);

	protected function setup($r)
	{
//		$this->user = $r->getUser();
	}

	public function getFootLengthJson($r)
	{
		$ds = new Dase_DBO_DataSet($this->db);
		$ds->is_published = 1;
		if ($r->get('eid')) {
			$ds->created_by = $r->get('eid');
		}

		$female_series = array();
		$female_series['name'] = 'Female';
		$female_series['color'] = 'rgba(223, 83, 83, .5)';
		$female_series['data'] = array();

		$male_series = array();
		$male_series['name'] = 'Male';
		$male_series['color'] ='rgba(119, 152, 191, .5)';
		$male_series['data'] = array();

		$line_series = array();
		$line_series['name'] = 'Regression Line';
		$line_series['type'] = 'line';
		$line_series['color'] = 'rgba(0,0,0,.5)';

		//G1 (female) - FL -  18 cm; Stride - 82.9 cm; Height - 122 cm
		//G3 (male 2) - FL - 20.9 cm; Stride - 87.6 cm; Height - 141 cm

		$g1_series = array();
		$g1_series['name'] = 'G1';
		$g1_series['type'] = 'line';
		$g1_series['color'] = 'rgba(250,150,150,.5)';
		$g1_series['data'] = array(array(18,90),array(18,190)); 

		$g3_series = array();
		$g3_series['name'] = 'G3';
		$g3_series['type'] = 'line';
		$g3_series['color'] = 'rgba(150,150,250,.5)';
		$g3_series['data'] = array(array(20.9,90),array(20.9,190)); 

		$x_coords = array();
		$y_coords = array();

		foreach ($ds->findAll(1) as $dataset) {
			foreach ($dataset->getPersonData() as $pd) {
				if ('m' == strtolower(substr($pd->gender,0,1))) {
					$x_coords[] = (float) $pd->foot_length;
					$y_coords[] = (float) $pd->height;
					$pair = array((float) $pd->foot_length,(float) $pd->height);
					$male_series['data'][] = $pair;
				}
				if ('f' == strtolower(substr($pd->gender,0,1))) {
					$x_coords[] = (float) $pd->foot_length;
					$y_coords[] = (float) $pd->height;
					$pair = array((float) $pd->foot_length,(float) $pd->height);
					$female_series['data'][] = $pair;
				}
			}
		}
		//creates an 'm' slope and 'b' intercept
		$linreg = Dase_Util::linReg($x_coords,$y_coords);
		$point1 = array(12,($linreg['m']*12)+$linreg['b']);
		$point2 = array(28,($linreg['m']*28)+$linreg['b']);
		$line_series['data'] = array($point1,$point2);

		$result = array();
		$result['data'] = array(
				$female_series,
				$male_series,
				$line_series,
				$g1_series,
				$g3_series,
				);
		$r->renderResponse(Dase_Json::get($result));
	}

	public function getStrideLengthJson($r)
	{
		$ds = new Dase_DBO_DataSet($this->db);
		$ds->is_published = 1;
		if ($r->get('eid')) {
			$ds->created_by = $r->get('eid');
		}

		$female_series = array();
		$female_series['name'] = 'Female';
		$female_series['color'] = 'rgba(223, 83, 83, .5)';
		$female_series['data'] = array();

		$male_series = array();
		$male_series['name'] = 'Male';
		$male_series['color'] ='rgba(119, 152, 191, .5)';
		$male_series['data'] = array();

		$line_series = array();
		$line_series['name'] = 'Regression Line';
		$line_series['type'] = 'line';
		$line_series['color'] = 'rgba(0,0,0,.5)';

		//G1 (female) - FL -  18 cm; Stride - 82.9 cm; Height - 122 cm
		//G3 (male 2) - FL - 20.9 cm; Stride - 87.6 cm; Height - 141 cm

		$g1_series = array();
		$g1_series['name'] = 'G1';
		$g1_series['type'] = 'line';
		$g1_series['color'] = 'rgba(250,150,150,.5)';
		$g1_series['data'] = array(array(82.9,90),array(82.9,190)); 

		$g3_series = array();
		$g3_series['name'] = 'G3';
		$g3_series['type'] = 'line';
		$g3_series['color'] = 'rgba(150,150,250,.5)';
		$g3_series['data'] = array(array(87.6,90),array(87.6,190)); 


		$x_coords = array();
		$y_coords = array();
		foreach ($ds->findAll(1) as $dataset) {
			foreach ($dataset->getPersonData() as $pd) {
				if ('m' == strtolower(substr($pd->gender,0,1))) {
					$x_coords[] = (float) $pd->stride_length;
					$y_coords[] = (float) $pd->height;
					$pair = array((float) $pd->stride_length,(float) $pd->height);
					$male_series['data'][] = $pair;
				}
				if ('f' == strtolower(substr($pd->gender,0,1))) {
					$x_coords[] = (float) $pd->stride_length;
					$y_coords[] = (float) $pd->height;
					$pair = array((float) $pd->stride_length,(float) $pd->height);
					$female_series['data'][] = $pair;
				}
			}
		}

		//creates an 'm' slope and 'b' intercept
		$linreg = Dase_Util::linReg($x_coords,$y_coords);
		$point1 = array(40,($linreg['m']*40)+$linreg['b']);
		$point2 = array(160,($linreg['m']*160)+$linreg['b']);
		$line_series['data'] = array($point1,$point2);

		$result = array();
		$result['data'] = array(
				$female_series,
				$male_series,
				$line_series,
				$g1_series,
				$g3_series,
				);
		$r->renderResponse(Dase_Json::get($result));
	}

	public function postToDatasets($r) 
	{
		$this->user = $r->getUser();
		if ($r->get('eid') == $this->user->eid && $r->get('name')) { 
			$ds = new Dase_DBO_DataSet($this->db);
			$ds->name = $r->get('name');
			$ds->created = date(DATE_ATOM);
			$ds->created_by = $this->user->eid;
			$ds->insert();
			$r->renderRedirect('trackways/data_sets');
		} else {
			$r->renderError(400);
		}
	}
}

