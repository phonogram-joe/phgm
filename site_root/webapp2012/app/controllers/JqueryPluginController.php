<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

ClassLoader::load('ApplicationController');

class JqueryPluginController extends ApplicationController
{
	public function initialize()
	{
		parent::initialize();
		$this->pageSection = 'jquery';
	}	

	public function index($params)
	{
		$this->pageTitle = 'jQueryプラグインの一覧';
		return $this->doRender();
	}

	public function google($params)
	{
		$this->pageTitle = 'jQueryのグーグルマップ';
		return $this->doRender();
	}

	public function tabs($params)
	{
		$this->pageTitle = 'jQueryのタブ';
		return $this->doRender();
	}
}