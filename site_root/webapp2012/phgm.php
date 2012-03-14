<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 *
 *	bootstrap.php
 *		フレームワークの必要なファイルをrequireして、初期化する
 */

//	ショートカット
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('ROOT_DIR', dirname(__FILE__));

class phgm
{
	private static $IS_INITIALIZED = false;

	//	webapp2012フレームワークの基本のフォルダー
	public static $APP_DIR;
	public static $CONFIG_DIR;
	public static $DATA_DIR;
	public static $LIB_DIR;
	public static $LIB_DATABASE_DIR;
	public static $LIB_VALIDATORS_DIR;
	public static $LIB_RENDERERS_DIR;
	public static $VENDOR_DIR;
	public static $LOG_DIR;
	public static $TMP_DIR;

	//	一時的なファイルのパス
	public static $DB_CACHE_DIR;
	public static $DATA_CACHE_DIR;
	public static $SESSION_DIR;
	public static $TEMPLATES_COMPILE_DIR;
	public static $TEMPLATES_CACHE_DIR;

	public static function classInitialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$IS_INITIALIZED = true;
		
		self::$APP_DIR = ROOT_DIR . DS . 'app';
		self::$CONFIG_DIR = ROOT_DIR . DS . 'config';
		self::$DATA_DIR = ROOT_DIR . DS . 'data';
		self::$LIB_DIR = ROOT_DIR . DS . 'lib';
		self::$LIB_DATABASE_DIR = self::$LIB_DIR . DS . 'database';
		self::$LIB_VALIDATORS_DIR = self::$LIB_DIR . DS . 'validators';
		self::$LIB_RENDERERS_DIR = self::$LIB_DIR . DS . 'renderers';
		self::$VENDOR_DIR = ROOT_DIR . DS . 'lib-vendor';
		self::$LOG_DIR = ROOT_DIR . DS . 'log';
		self::$TMP_DIR = ROOT_DIR . DS . 'tmp';

		//	一時的なファイルのパス
		self::$DB_CACHE_DIR = self::$TMP_DIR . DS . 'db_cache';
		self::$DATA_CACHE_DIR = self::$TMP_DIR . DS . 'data_cache';
		self::$SESSION_DIR = self::$TMP_DIR . DS . 'session';
		self::$TEMPLATES_COMPILE_DIR = self::$TMP_DIR . DS . 'templates_compile';
		self::$TEMPLATES_CACHE_DIR = self::$TMP_DIR . DS . 'templates_cache';
	}

	public static function go($routeName = null, $routeParams = null)
	{
		self::classInitialize();
		self::loadBase();
		self::loadConfig();
		self::doRequest($routeName, $routeParams);
	}

	public static function doRequest($routeName = null, $routeParams = null)
	{
		try {
			$httpHandler = new HttpHandler();
			$httpHandler->handleRequest($routeName, $routeParams);
		} catch (Exception $e) {
			try {
				Logger::fatal($e);
				/*TODO: show fatal errors in dev/test
				$environment = Config::get(Config::ENVIRONMENT);
				if ($environment === Config::ENVIRONMENT_DEVELOPMENT || $environment === Config::ENVIRONMENT_TEST) {
				*/
					print '<p>' . $e->getMessage() . '</p>';
					print '<p>' . nl2br($e->getTraceAsString()) . '</p>';
				/*
				}
				*/
				print '<p>' . Config::get(Config::FATAL_ERROR_MESSAGE) . '</p>';
			} catch (Exception $e) {
				Logger::fatal($e);
			}
		}
	}

	private static function loadBase()
	{
		require_once(self::$LIB_DIR . DS . 'ClassLoader.php');
		ClassLoader::classInitialize();
		//	フレームワークの基本クラス
		ClassLoader::loadFrom('Config', self::$LIB_DIR);
		ClassLoader::loadFrom('Logger', self::$LIB_DIR);
		ClassLoader::loadFrom('TimeUtils', self::$LIB_DIR);
		ClassLoader::loadFrom('StringUtils', self::$LIB_DIR);
		ClassLoader::loadFrom('ModelDefinition', self::$LIB_DIR);
		ClassLoader::loadFrom('BaseModel', self::$LIB_DIR);
		ClassLoader::loadFrom('BaseDecorator', self::$LIB_DIR);
		ClassLoader::loadFrom('BaseController', self::$LIB_DIR);
		ClassLoader::loadFrom('BaseRenderer', self::$LIB_DIR);
		ClassLoader::loadFrom('Router', self::$LIB_DIR);
		ClassLoader::loadFrom('Session', self::$LIB_DIR);
		ClassLoader::loadFrom('SessionUser', self::$LIB_DIR);
		ClassLoader::loadFrom('HttpRequest', self::$LIB_DIR);
		ClassLoader::loadFrom('HttpResponse', self::$LIB_DIR);
		ClassLoader::loadFrom('HttpResponseFormat', self::$LIB_DIR);
		ClassLoader::loadFrom('HttpHandler', self::$LIB_DIR);

		//	データベースのクラス
		ClassLoader::loadFrom('WhereClause', self::$LIB_DATABASE_DIR);
		ClassLoader::loadFrom('DatabaseSession', self::$LIB_DATABASE_DIR);
		ClassLoader::loadFrom('DB', self::$LIB_DATABASE_DIR);
		ClassLoader::loadFrom('DbModel', self::$LIB_DATABASE_DIR);
	}

	private static function loadConfig()
	{
		//	サイトの動きに関する設定
		require_once(self::$CONFIG_DIR . DS . 'app_all.php'); //appの設定
		Config::readEnvironment(); //環境設定によりコンフィグする
		$environment = Config::get(Config::ENVIRONMENT);
		if ($environment === Config::ENVIRONMENT_PRODUCTION) {
			require_once(self::$CONFIG_DIR . DS . 'app_' . Config::ENVIRONMENT_PRODUCTION . '.php'); //公開環境のみの設定
		} else if ($environment === Config::ENVIRONMENT_DEVELOPMENT) {
			require_once(self::$CONFIG_DIR . DS . 'app_' . Config::ENVIRONMENT_DEVELOPMENT . '.php'); //開発環境のみの設定
		} else if ($environment === Config::ENVIRONMENT_TEST) {
			require_once(self::$CONFIG_DIR . DS . 'app_' . Config::ENVIRONMENT_TEST . '.php'); //開発環境のみの設定
		}
		
		//	サイトのURLー＞コントローラ・アクション設定
		require_once(self::$CONFIG_DIR . DS . 'routes.php');
		defineRoutes(Router::getRouter());

		//	データベースの接続情報を設定する
		require_once(self::$CONFIG_DIR . DS . 'database.php');
		defineDatabases();
	}
}
