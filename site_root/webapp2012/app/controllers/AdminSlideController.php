<?php

ClassLoader::load('AdminController');
ClassLoader::load('PresentationIndexForm');
ClassLoader::load('PresentationModel');

class AdminSlideController extends AdminController
{
	public function initialize()
	{
		parent::initialize();
	}

	public function slides($params)
	{
		$this->pageTitle = 'プレゼン作成';
		$this->presentation = PresentationModel::loadId($params['id']);
		$lastSlide = is_null($this->presentation->slides) ? null : end($this->presentation->slides);
		$nextOrderBy = is_null($lastSlide) ? 0 : $lastSlide->val('order_by') + 1;
		$this->newSlide = PresentationSlideModel::createFor($this->presentation->id);
		$this->newSlide->val('order_by', $nextOrderBy);
		return $this->doRender();
	}

	public function slidePreview($params)
	{
		return $this->doRenderDataAs(array('slide' => $params['slide']));
	}

	public function editSlides($params)
	{
		$error = null;
		if (false === array_key_exists('slide', $params)) {
			$this->doRenderDataAs(
				array('error' => 'データは指定されてないです。'),
				HttpResponseFormat::$JSON
			);
		}
		$this->presentation = PresentationModel::loadId($params['id']);
		$db = DB::getSession();
		foreach ($this->presentation->slides as $slide) {
			if (!array_key_exists($slide->id, $params['slide'])) {
				$error = 'スライドのデータが抜いています。';
				break;
			}
			$slide->set($params['slide'][$slide->id]);
			if ($params['slide'][$slide->id]['delete'] === '1') {
				$db->delete($slide);
			} else {
				$db->track($slide);
			}
		}
		if (!is_null($error)) {
			return $this->doRenderDataAs(
				array('error' => $error),
				HttpResponseFormat::$JSON
			);
		}

		$db->flush();

		return $this->doRenderDataAs(
			array('success' => 'スライドを更新しました。'),
			HttpResponseFormat::$JSON
		);
	}

	public function newSlide($params)
	{
		$this->presentation = PresentationModel::loadId($params['id']);
		if (is_null($this->presentation)) {
			return $this->doError('プレゼンが見つかりません。');
		}
		$this->newSlide = PresentationSlideModel::createFor($this->presentation->id);
		
		$this->newSlide->set($params);
		if (!$this->newSlide->isValid()) {
			return $this->doRenderAction('slides');
		}

		$db = DB::getSession();
		$db->track($this->newSlide);
		$db->flush();

		return $this->doRedirectWithMessage(
			'admin:presentation_slides',
			array('id' => $this->presentation->id),
			ApplicationController::MSG_ALERT,
			'スライドを追加しました。'
		);
	}
}