<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

/*
	TODO: use PHP 5's __autoload($name) feature to autoload classes using naming conventions

public function __autoload($name)
{
	ClassLoader::load($name);
}
*/

class ClassLoader
{
	/**
	 *	コントローラクラスのフラグ
	 */
	const CONTROLLER = 'Controller';

	/**
	 *	共通ロジック（助かるクラス）のフラグ
	 */
	const HELPER = 'Helper';

	/**
	 *	DBレコードを代表するモデルクラスのフラグ
	 */
	const MODEL = 'Model';

	/**
	 *	フォームデータを代表するクラスのフラグ
	 */
	const FORM = 'Form';

	/**
	 *	モデルオブジェクトのデータを表示するクラスのフラグ
	 */
	const DECORATOR = 'Decorator';

	/**
	 *	データの有効性を確認クラスのフラグ
	 */
	const VALIDATOR = 'Validator';

	/**
	 *	データ形を変換するクラス
	 */
	const CONVERTER = 'Converter';

	const DATATYPE = 'Type';

	/**
	 *	レンダラクラスのフラグ。コントローラのデータをあるデータ形に変換するクラス
	 */
	const RENDERER = 'Renderer';

	public static $IS_INITIALIZED = false;
	public static $LOADED_CLASSES;

	//	app専用のファイル
	public static $APP_CONTROLLERS_DIR; //コントローラクラス
	public static $APP_MODELS_DIR; //DBやフォームのモデルクラス
	public static $APP_DECORATORS_DIR; //モデルを表示する際に使うクラス
	public static $APP_VIEWS_DIR; //共通のレイアウト・ガジェットと、コントローラのアクションごとのテンプレート

	//	app専用の共通クラス
	public static $APP_LIB_DIR; //共通のクラスやビュープラグイン
	public static $APP_VALIDATORS_DIR; //モデルのデータがデータ形や規則にあってるか確認するクラス。
	public static $APP_CONVERTERS_DIR; //モデルにset()する時にデータを変換するクラス
	public static $APP_RENDERERS_DIR; //カスタムなレンダラクラス。Smartyのようにデータをテンプレートに埋め込むクラスです。
	public static $APP_TYPES_DIR;
	public static $APP_HELPERS_DIR; //共通ロジック
	public static $APP_SMARTY_DIR; //カスタムなSmartyのプラグイン

	public static function classInitialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$IS_INITIALIZED = true;
		self::$LOADED_CLASSES = array();

		//	app専用のファイル
		self::$APP_CONTROLLERS_DIR = phgm::$APP_DIR . DS . 'controllers'; //コントローラクラス
		self::$APP_MODELS_DIR = phgm::$APP_DIR . DS . 'models'; //DBやフォームのモデルクラス
		self::$APP_DECORATORS_DIR = phgm::$APP_DIR . DS . 'decorators'; //モデルを表示する際に使うクラス
		self::$APP_VIEWS_DIR = phgm::$APP_DIR . DS . 'views'; //共通のレイアウト・ガジェットと、コントローラのアクションごとのテンプレート

		//	app専用の共通クラス
		self::$APP_LIB_DIR = phgm::$APP_DIR . DS . 'lib'; //共通のクラスやビュープラグイン
		self::$APP_VALIDATORS_DIR = self::$APP_LIB_DIR . DS . 'validators'; //モデルのデータがデータ形や規則にあってるか確認するクラス。
		self::$APP_CONVERTERS_DIR = self::$APP_LIB_DIR . DS . 'converters'; 
		self::$APP_RENDERERS_DIR = self::$APP_LIB_DIR . DS . 'renderers'; //カスタムなレンダラクラス。Smartyのようにデータをテンプレートに埋め込むクラスです。
		self::$APP_TYPES_DIR = self::$APP_LIB_DIR . DS . 'datatypes';
		self::$APP_HELPERS_DIR = self::$APP_LIB_DIR . DS . 'helpers'; //共通ロジック
		self::$APP_SMARTY_DIR = self::$APP_LIB_DIR . DS . 'smarty-plugins'; //カスタムなSmartyのプラグイン
	}

	/**
	 *	load($name)
	 *		クラスをロードする。
	 *	@param $name String requireするクラス名
	 *	@return none
	 */
	public static function load($name)
	{
		if (false !== array_search($name, self::$LOADED_CLASSES)) {
			return;
		}
		if (preg_match('/Controller$/', $name)) {
			self::loadFrom($name, self::$APP_CONTROLLERS_DIR);
		} else if (preg_match('/(Model|Form)$/', $name)) {
			self::loadFrom($name, self::$APP_MODELS_DIR);
		} else if (preg_match('/Decorator$/', $name)) {
			self::loadFrom($name, self::$APP_DECORATORS_DIR);
		} else if (preg_match('/Helper$/', $name)) {
			self::loadFrom($name, self::$APP_HELPERS_DIR);
		} else if (preg_match('/Validator$/', $name)) {
			if (file_exists(self::path(self::$APP_VALIDATORS_DIR, $name))) {
				self::loadFrom($name, self::$APP_VALIDATORS_DIR);
			} else {
				self::loadFrom($name, phgm::$LIB_VALIDATORS_DIR);
			}
		} else if (preg_match('/Renderer$/', $name)) {
			if (file_exists(self::path(self::$APP_RENDERERS_DIR, $name))) {
				self::loadFrom($name, self::$APP_RENDERERS_DIR);
			} else {
				self::loadFrom($name, phgm::$LIB_RENDERERS_DIR);
			}
		} else if (preg_match('/Converter$/', $name)) {
			if (file_exists(self::path(self::$APP_CONVERTERS_DIR, $name))) {
				self::loadFrom($name, self::$APP_CONVERTERS_DIR);
			} else {
				self::loadFrom($name, phgm::$LIB_CONVERTERS_DIR);
			}
		} else if (preg_match('/Type$/', $name)) {
			if (file_exists(self::path(self::$APP_TYPES_DIR, $name))) {
				self::loadFrom($name, self::$APP_TYPES_DIR);
			} else {
				self::loadFrom($name, phgm::$LIB_TYPES_DIR);
			}
		}
		self::$LOADED_CLASSES[] = $name;
	}

	public static function loadFrom($className, $folder)
	{
		$path = self::path($folder, $className);
		if (!file_exists($path)) {
			throw new Exception('ClassLoader::load -- ファイルは見つかりません: [' . $path . ']');
		}
		require_once($path);
		if (method_exists($className, 'classInitialize')) {
			call_user_func(array($className, 'classInitialize'));
		}
	}

	public static function path($folder, $className)
	{
		return $folder . DS . $className . '.php';
	}

	/**
	 *	toClassName($prefix[, $type])
	 *		「login_user」のようなテキストを「LoginUserModel」のようなクラス名の形に変換する。
	 *
	 *	@param $prefix String 変換するストリング。
	 *	@param type String (任意) Model・Validator・Controllerのような、クラスの種類を区別するストリング。
	 *	@return String <prefix>をクラス名の形に変換した結果。<type>が指定される場合は、最後に付けられる。
	 */
	public static function toClassName($prefix, $type = null)
	{
		return ucfirst(self::underscoresToCamel($prefix)) . (is_null($type) ? '' : $type);
	}

	/**
	 *	classNamePrefix($class)
	 *		「...Model」や「...Controller」のようなクラス名の接尾辞を抜いてクラス名のベースを返す。
	 *
	 *	@access public
	 *	@param $class String クラス名
	 *	@return String 接尾辞を抜いてクラス名
	 */
	public static function classNamePrefix($class)
	{
		return self::camelToUnderscores(preg_replace('/(Controller|Model|Form|Decorator|Renderer|Validator|Converter|Helper)$/', '', $class));
	}

	/*
	 *	camelToUnderscores($string)
	 *		'className' => 'class_name'
	 */
	public static function camelToUnderscores($string)
	{
		$toUnderscore = create_function('$c', 'return $c[1] . \'_\' . strtolower($c[2]);');
		return strtolower(preg_replace_callback('/([a-z0-9])([A-Z])/', $toUnderscore, $string));
	}

	/*
	 *	underscoresToCamel($string)
	 *		'class_name' => 'className'
	 */
	public static function underscoresToCamel($string)
	{
		$toCamel = create_function('$c', 'return strtoupper($c[1]);');
    	return preg_replace_callback('/_([a-z])/', $toCamel, $string);
	}
}