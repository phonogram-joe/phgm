<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

ClassLoader::load(MODEL, 'SampleModel');

class SampleController extends BaseController
{
	public $message = 'Hello There! 宜しくお願いします！';
	public $fmt;
	public $id;

	public function index($params)
	{
		$this->fmt = $this->getRenderFormat();
		return $this->doRender();
	}

	public function editForm($params)
	{
		$this->id = $params['id'];
		return $this->doRender(null, null, 'show');
	}

	public function show($params)
	{
		$this->id = $params['id'];

		$this->smodel = new SampleModel();
		//$this->smodel->set('name', 'スミス');
		$this->smodel->set('email', 'smith@example.com@');
		if (!$this->smodel->isValid()) {
			$this->smodelErrors = $this->smodel->getValidationErrors();
		}
		return $this->doRender();

	}
	public function editSave($params)
	{
		
	}
}