<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

define('CONTROLLER', CONTROLLERS_DIR); //コントローラクラス
define('APP_LIB', APP_LIB_DIR . 'classes'); //共通のクラスやビュープラグイン
define('MODEL', MODELS_DIR); //DBやフォームのモデルクラス
define('VIEW_MODEL', VIEW_MODELS_DIR); //モデルを表示する際に使うクラス
define('VIEW', VIEWS_DIR); //共通のレイアウト・ガジェットと、コントローラのアクションごとのテンプレート

/*
//TODO: use PHP 5's __autoload($name) feature to autoload classes using naming conventions
function __autoload($name)
{
	if (preg_match('/Controller/', $name)) {
		ClassLoader::load(CONTROLLER, $name);
	}
	if (preg_match('/Model/', $name)) {
		ClassLoader::load(MODEL, $name);
	}
	if (preg_match('/ViewModel/', $name)) {
		ClassLoader::load(VIEW_MODEL, $name);
	}
	if (preg_match('/View/', $name)) {
		ClassLoader::load(VIEW, $name);
	}
	if (preg_match('/Helper/', $name)) {
		ClassLoader::load(APP_LIB, $name);
	}
}
*/

class ClassLoader
{
	public static function load($type, $filename)
	{
		$path = self::path($type, $filename);
		if (!file_exists($path)) {
			throw new Exception('ClassLoader::load -- ファイルは見つかりません: ' . $path);
		}
		require_once(self::path($type, $filename));
	}

	private static function path($type, $filename)
	{
		return $type . DS . preg_replace('/\.+/', DS, $filename) . '.php';
	}

	public static function classNamePrefix($class)
	{
		return StringUtils::camelToUnderscores(preg_replace('/(Controller|Model|ViewModel|Renderer)$/', '', $class));
	}
}