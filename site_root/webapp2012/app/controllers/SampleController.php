<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

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

	public function show($params)
	{
		$this->id = $params['id'];
		return $this->doRender();
	}

	public function editForm($params)
	{
		$this->id = $params['id'];
		$this->urlName = Router::getRouter()->urlForName('restSample_new_form', array('id' => 5));
		$this->urlRoute = Router::getRouter()->urlForRoute('SampleController', 'editForm', array('id' => 5));
		return $this->doRender(null, null, 'show');
	}
	public function editSave($params)
	{
		
	}
}