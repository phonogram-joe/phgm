<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

require_once(VENDOR_DIR . DS . 'smarty' . DS . 'libs' . DS . 'Smarty.class.php');

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
		return $this->smarty->fetch($this->templatePath);
	}

	public function customHttpResponse($data, $httpResponse)
	{
		
	}

	public function initialize()
	{
		$this->setup();
	}

	public function setup()
	{
		$this->smarty = new Smarty();
		$this->smarty->setTemplateDir(VIEWS_DIR);
		$this->smarty->setCompileDir(TEMPLATES_COMPILE_DIR);
		$this->smarty->setConfigDir('');
		$this->smarty->setCacheDir(TEMPLATES_CACHE_DIR);
		$this->smarty->addPluginsDir(LIB_DIR . DS . 'smarty-plugins');
		$this->smarty->addPluginsDir(APP_LIB_DIR . DS . 'smarty-plugins');

		$this->smarty->left_delimiter = SMARTY_LEFT_DELIMITER;
		$this->smarty->right_delimiter = SMARTY_RIGHT_DELIMITER;
	}
}