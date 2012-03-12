<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

ClassLoader::load('SampleModel');

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
			return $this->doRenderAction('newForm');
		}
		$db->track($this->model);
		$db->flush();
		return $this->doRedirect('sample_show', array('id' => $this->model->get('id')));
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
		if (is_null($this->model)) {
			return $this->doError('サンプルモデルが見つかりません。');
		}
		$this->model->set($params);
		if (!$this->model->isValid()) {
			return $this->doRenderAction('editForm');
		}
		$db->track($this->model);
		$db->flush();
		return $this->doRedirect('sample_show', array('id' => $this->id));
	}

	public function deleteForm($params)
	{
		$this->id = $params['id'];
		$db = DB::getSession();
		$this->model = $db->find('SampleModel', $this->id);
		if (is_null($this->model)) {
			return $this->doError('サンプルモデルが見つかりません。');
		}
		return $this->doRender();
	}

	public function deleteSave($params)
	{
		$this->id = $params['id'];
		$db = DB::getSession();
		$this->model = $db->find('SampleModel', $this->id);
		if (is_null($this->model)) {
			return $this->doError('サンプルモデルが見つかりません。');
		}
		$db->delete($this->model);
		if (!$db->flush()) {
			$this->pageError = '削除できませんでした。';
			return $this->doRenderAction('deleteForm');
		}
		return $this->doRedirect('sample_index');
	}
}