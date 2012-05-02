<?php

ClassLoader::load('PresentationModel');

class SlideViewerController extends BaseController
{
	public function initialize()
	{
		parent::initialize();
	}

	public function show($params)
	{
		$this->pageTitle = 'プレゼン表示';
		$this->presentation = PresentationModel::loadId($params['id']);
		if (is_null($this->presentation)) {
			return $this->doError('プレゼンが見つかりません。');
		}
		return $this->doRender();
	}


	public function play($params)
	{
		$this->pageTitle = 'プレゼン表示';
		$this->presentation = PresentationModel::loadId($params['id']);
		if (is_null($this->presentation)) {
			return $this->doError('プレゼンが見つかりません。');
		}
		return $this->doRender();
	}
}