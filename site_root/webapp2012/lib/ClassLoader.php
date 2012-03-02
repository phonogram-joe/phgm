<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

define('CONTROLLER', CONTROLLERS_DIR); //コントローラクラス
define('HELPER', APP_LIB_DIR . DS . 'classes'); //共通のクラスやビュープラグイン
define('MODEL', MODELS_DIR); //DBやフォームのモデルクラス
define('DECORATOR', DECORATORS_DIR); //モデルを表示する際に使うクラス
define('VIEW', VIEWS_DIR); //共通のレイアウト・ガジェットと、コントローラのアクションごとのテンプレート
define('LIB_VALIDATOR', LIB_DIR . DS . 'validators');
define('VALIDATOR', VALIDATORS_DIR);
define('LIB_RENDERER', LIB_DIR . DS . 'renderers');
define('RENDERER', RENDERERS_DIR);

/*
//TODO: use PHP 5's __autoload($name) feature to autoload classes using naming conventions
*/

class ClassLoader
{

	public static function load($type, $name)
	{
		if (preg_match('/Controller$/', $name)) {
			self::loadType(CONTROLLER, $name);
		} else if (preg_match('/Model$/', $name)) {
			self::loadType(MODEL, $name);
		} else if (preg_match('/Decorator$/', $name)) {
			self::loadType(DECORATOR, $name);
		} else if (preg_match('/View$/', $name)) {
			self::loadType(VIEW, $name);
		} else if (preg_match('/Helper$/', $name)) {
			self::loadType(HELPER, $name);
		} else if (preg_match('/Validator$/', $name)) {
			if (file_exists(self::path(VALIDATOR, $name))) {
				self::loadType(VALIDATOR, $name);
			} else {
				self::loadType(LIB_VALIDATOR, $name);
			}
		} else if (preg_match('/Renderer/', $name)) {
			if (file_exists(self::path(RENDERER, $name))) {
				self::loadType(RENDERER, $name);
			} else {
				self::loadType(LIB_RENDERER, $name);
			}
		}
	}

	private static function loadType($type, $filename)
	{
		$path = self::path($type, $filename);
		if (!file_exists($path)) {
			throw new Exception('ClassLoader::load -- ファイルは見つかりません: ' . $path);
		}
		require_once(self::path($type, $filename));
		if (method_exists($filename, 'classInitialize')) {
			call_user_func(array($filename, 'classInitialize'));
		}
	}

	private static function path($type, $filename)
	{
		return $type . DS . preg_replace('/\.+/', DS, $filename) . '.php';
	}

	public static function classNamePrefix($class)
	{
		return StringUtils::camelToUnderscores(preg_replace('/(Controller|Model|Decorator|Renderer|Validator|Helper)$/', '', $class));
	}
}