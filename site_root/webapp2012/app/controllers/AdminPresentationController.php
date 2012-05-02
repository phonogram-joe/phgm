<?php

ClassLoader::load('AdminController');
ClassLoader::load('PresentationIndexForm');
ClassLoader::load('PresentationModel');

class AdminPresentationController extends AdminController
{
	public function initialize()
	{
		parent::initialize();
	}

	public function index($params)
	{
		$this->pageTitle = 'プレゼンテーション一覧';
		$this->search = PresentationIndexForm::create();
		if (false !== array_key_exists('search', $params)) {
			$this->search->set($params['search']);
		}
		if (!$this->search->isValid()) {
			$this->search = PresentationIndexForm::create();
		}
		$this->pageSubTitle = $this->search->hasConditions() ? '検索結果' : '全てのクライアント';
		$this->presentations = PresentationModel::loadAll($this->search);
		return $this->doRender();
	}

	public function show($params)
	{
		$this->pageTitle = 'プレゼンテーション詳細';
		$this->presentation = PresentationModel::loadId($params['id']);
		if (is_null($this->presentation)) {
			return $this->doError('プレゼンテーションが見つかりません。');
		}
		return $this->doRender();
	}

	public function editForm($params)
	{
		$this->pageTitle = 'プレゼンテーション編集';
		$this->presentation = PresentationModel::loadId($params['id']);
		if (is_null($this->presentation)) {
			return $this->doError('プレゼンテーションが見つかりません。');
		}
		return $this->doRender();
	}

	public function editSave($params)
	{
		$this->pageTitle = 'プレゼンテーション編集';
		$this->presentation = PresentationModel::loadId($params['id']);
		if (is_null($this->presentation)) {
			return $this->doError('プレゼンテーションが見つかりません。');
		}
		$this->presentation->set($params);
		if (!$this->presentation->isValid()) {
			return $this->doRenderAction('editForm');
		}
		$db = DB::getSession();
		$db->track($this->presentation);
		$db->flush();
		return $this->doRedirectWithMessage(
			'admin:presentation_show',
			array('id' => $this->presentation->id),
			ApplicationController::MSG_ALERT,
			'プレゼンテーションを保存しました'
		);
	}

	public function newForm($params)
	{
		$this->pageTitle = 'プレゼンテーション登録';
		$this->presentation = PresentationModel::create();
		return $this->doRender();
	}

	public function newSave($params)
	{
		$this->pageTitle = 'プレゼンテーション登録';
		$this->presentation = PresentationModel::create();
		$this->presentation->set($params);
		if (!$this->presentation->isValid()) {
			return $this->doRenderAction('newForm');
		}

		$db = DB::getSession();
		$db->track($this->presentation);
		$db->flush();
		return $this->doRedirectWithMessage(
			'admin:presentation_index',
			array(),
			ApplicationController::MSG_ALERT,
			'プレゼンテーションを登録しました。'
		);
	}
}