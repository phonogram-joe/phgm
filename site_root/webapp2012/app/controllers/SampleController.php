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
		$this->samples = DB::getSession()->query('SampleModel');
		return $this->doRender();
	}

	public function show($params)
	{
		$this->id = $params['id'];
		$db = DB::getSession();
		$this->model = $db->find('SampleModel', $this->id);
		if (is_null($this->model)) {
			return $this->doError('サンプルモデルが見つかりません。');
		}
		return $this->doRender();

	}

	public function newForm($params)
	{
		$this->model = new SampleModel();
		return $this->doRender();
	}
	public function newSave($params)
	{
		$db = DB::getSession();
		$this->model = new SampleModel();
		$this->model->set($params);
		if (!$this->model->isValid()) {
			return $this->doRender(null, null, 'newForm');
		}
		$db->track($this->model);
		$db->flush();
		//return $this->doRedirect('sample_show', array('id' => $this->model->get('id')));
		return $this->doRedirect('SampleController', 'show', array('id' => $this->model->get('id')));
	}

	public function editForm($params)
	{
		$this->id = $params['id'];
		$db = DB::getSession();
		$this->model = $db->find('SampleModel', $this->id);
		if (is_null($this->model)) {
			return $this->doError('サンプルモデルが見つかりません。');
		}
		return $this->doRender();
	}

	public function editSave($params)
	{
		$this->id = $params['id'];
		$db = DB::getSession();
		$this->model = $db->find('SampleModel', $this->id);
		$this->model->set($params);
		if (!$this->model->isValid()) {
			return $this->doRender(null, null, 'editForm');
		}
		$db->track($this->model);
		$db->flush();
		return $this->doRedirect('sample_show', array('id' => $this->id));
	}
}