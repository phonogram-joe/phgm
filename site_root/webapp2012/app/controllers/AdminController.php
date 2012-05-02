<?php

ClassLoader::load('ApplicationController');

/*
 *	AdminController
 *		管理者ようのコントローラのスーパークラス
 */
class AdminController extends ApplicationController
{
	public function initialize()
	{
		parent::initialize();
		//$this->addFilter(BaseController::$BEFORE_FILTER, 'ensureAdmin');
	}	

	public function ensureAdmin($params)
	{
		if (!$this->login->isAdminUser()) {
			return $this->doError('アクセスしたURLは管理者ようです。');
		}
	}
}