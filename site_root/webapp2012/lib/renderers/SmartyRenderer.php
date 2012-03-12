<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

require_once(phgm::$VENDOR_DIR . DS . 'smarty' . DS . 'libs' . DS . 'Smarty.class.php');

class SmartyRenderer extends BaseRenderer
{
	private $smarty;

	public function customRender($data, $httpResponse)
	{
		foreach ($data as $key => $value) {
			if (is_object($value)) {
				//NOTE: assignByRef(...)を使ってみたら設定されたキーは上書きされるときもある
				$this->smarty->assign($key, $value);
			} else {
				$this->smarty->assign($key, $value);
			}
		}
		if (!file_exists($this->templatePath)) {
			throw new Exception('SmartyRenderer:customRender() -- テンプレートファイル' . $this->templatePath . 'は見つかりません。');
		}
		return $this->smarty->fetch($this->templatePath);
	}

	public function customHttpResponse($data, $httpResponse)
	{
		
	}

	public function initialize()
	{
		$this->smarty = new Smarty();
		$this->smarty->setTemplateDir(ClassLoader::$APP_VIEWS_DIR);
		$this->smarty->setCompileDir(phgm::$TEMPLATES_COMPILE_DIR);
		$this->smarty->setConfigDir('');
		$this->smarty->setCacheDir(phgm::$TEMPLATES_CACHE_DIR);
		$this->smarty->addPluginsDir(phgm::$LIB_DIR . DS . 'smarty-plugins');
		$this->smarty->addPluginsDir(ClassLoader::$APP_LIB_DIR . DS . 'smarty-plugins');

		$this->smarty->left_delimiter = Config::get(Config::SMARTY_LEFT_DELIMITER);
		$this->smarty->right_delimiter = Config::get(Config::SMARTY_RIGHT_DELIMITER);
	}
}