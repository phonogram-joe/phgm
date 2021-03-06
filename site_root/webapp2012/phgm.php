<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 *
 *	phgm.php
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
	public static $LIB_CONVERTERS_DIR;
	public static $LIB_RENDERERS_DIR;
	public static $LIB_TYPES_DIR;
	public static $VENDOR_DIR;
	public static $LOG_DIR;
	public static $TMP_DIR;

	//	一時的なファイルのパス
	public static $DB_CACHE_DIR;
	public static $DATA_CACHE_DIR;
	public static $SESSION_DIR;
	public static $TEMPLATES_COMPILE_DIR;
	public static $TEMPLATES_CACHE_DIR;
	public static $UPLOAD_FILES_DIR;

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
		self::$LIB_CONVERTERS_DIR = self::$LIB_DIR . DS . 'converters';
		self::$LIB_RENDERERS_DIR = self::$LIB_DIR . DS . 'renderers';
		self::$LIB_TYPES_DIR = self::$LIB_DIR . DS . 'datatypes';
		self::$VENDOR_DIR = ROOT_DIR . DS . 'lib-vendor';
		self::$LOG_DIR = ROOT_DIR . DS . 'log';
		self::$TMP_DIR = ROOT_DIR . DS . 'tmp';

		//	一時的なファイルのパス
		self::$DB_CACHE_DIR = self::$TMP_DIR . DS . 'db_cache';
		self::$DATA_CACHE_DIR = self::$TMP_DIR . DS . 'data_cache';
		self::$SESSION_DIR = self::$TMP_DIR . DS . 'session';
		self::$TEMPLATES_COMPILE_DIR = self::$TMP_DIR . DS . 'templates_compile';
		self::$TEMPLATES_CACHE_DIR = self::$TMP_DIR . DS . 'templates_cache';
		self::$UPLOAD_FILES_DIR = self::$TMP_DIR . DS . 'uploads';
	}

	public static function go($routeName = null, $routeParams = null)
	{
		try {
			set_error_handler(array('phgm', 'handleError'), E_ERROR | E_RECOVERABLE_ERROR);
			self::classInitialize();
			self::loadBase();
			self::loadConfig();

			$httpHandler = new HttpHandler();
			$httpHandler->handleRequest($routeName, $routeParams);
		} catch (Exception $e) {
			try {
				$message = '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
				$traces = explode("\n", $e->getTraceAsString());
				$message .= '<p>';
				foreach ($traces as $line) {
					$message .= htmlspecialchars($line) . '<br>';
				}
				$message .= '</p>';

				self::handleError('不明', $message, $e->getFile(), $e->getLine());
			} catch (Exception $e) {
				
			}
		}
	}

	public static function handleError($errorNumber, $errorMessage = null, $errorFile = null, $errorLine = null, $errorContext = array())
	{
		$errorFile = is_null($errorFile) ? '' : $errorFile;
		$errorLine = is_null($errorLine) ? '' : $errorLine;
		print '<!doctype html><html lang="ja"><head><meta charset="utf-8"></head><body>';
		print '<p>' . Config::get(Config::FATAL_ERROR_MESSAGE) . '</p>';
		if (Config::isDevTest()) {
			print '<p>' . htmlspecialchars($errorFile) . ' ' . htmlspecialchars($errorLine). '</p>';
			print $errorMessage;
		}
		print '</body></html>';
		Logger::fatal("$errorNumber $errorMessage $errorFile $errorLine");
		die();
	}

	private static function loadBase()
	{
		require_once(self::$LIB_DIR . DS . 'ClassLoader.php');
		ClassLoader::classInitialize();
		//	フレームワークの基本クラス
		ClassLoader::loadFile('Config', self::$LIB_DIR, true);
		ClassLoader::loadFile('Logger', self::$LIB_DIR, true);
		ClassLoader::loadFile('Profiler', self::$LIB_DIR, true);
		ClassLoader::loadFile('TimeUtils', self::$LIB_DIR, true);
		ClassLoader::loadFile('BaseDataType', self::$LIB_TYPES_DIR, true);
		ClassLoader::loadFile('ModelDefinition', self::$LIB_DIR);
		ClassLoader::loadFile('BaseModel', self::$LIB_DIR, true);
		ClassLoader::loadFile('BaseDecorator', self::$LIB_DIR);
		ClassLoader::loadFile('BaseController', self::$LIB_DIR);
		ClassLoader::loadFile('BaseRenderer', self::$LIB_DIR, true);
		ClassLoader::loadFile('ModelRenderFormat', self::$LIB_DIR);
		ClassLoader::loadFile('Router', self::$LIB_DIR);
		ClassLoader::loadFile('Session', self::$LIB_DIR);
		ClassLoader::loadFile('SessionUser', self::$LIB_DIR);
		ClassLoader::loadFile('FileUpload', self::$LIB_DIR, true);
		ClassLoader::loadFile('HttpRequest', self::$LIB_DIR);
		ClassLoader::loadFile('HttpResponse', self::$LIB_DIR);
		ClassLoader::loadFile('HttpResponseFormat', self::$LIB_DIR, true);
		ClassLoader::loadFile('HttpHandler', self::$LIB_DIR);

		//	データベースのクラス
		ClassLoader::loadFile('SqlStatement', self::$LIB_DATABASE_DIR);
		ClassLoader::loadFile('DatabaseSession', self::$LIB_DATABASE_DIR);
		ClassLoader::loadFile('PhgmPDO', self::$LIB_DATABASE_DIR);
		ClassLoader::loadFile('DB', self::$LIB_DATABASE_DIR, true);
		ClassLoader::loadFile('DbModel', self::$LIB_DATABASE_DIR, true);
		ClassLoader::loadFile('JoinStatement', self::$LIB_DATABASE_DIR);
	}

	private static function loadConfig()
	{
		//	サイトの動きに関する設定
		require_once(self::$CONFIG_DIR . DS . 'app_all.php'); //appの設定
		$environment = Config::readEnvironment();
		if (is_null($environment) || !is_string($environment)) {
			throw new Exception('phgm::loadConfig() -- 環境設定は無効です。');
		}
		$environmentFile = self::$CONFIG_DIR . DS . 'app_' . $environment . '.php';
		if (!file_exists($environmentFile)) {
			throw new Exception('phgm::loadConfig() -- 環境設定ファイルは見つかりません。 ' . $environmentFile);
		}
		require_once($environmentFile);

		//	メモリー・ファイル・DBクエリの解析を有効にする。(開発とテストの環境のみ)
		if (Config::isDevTest()) {
			Profiler::getProfiler()->start(PHGM_START_TIME);
		}

		//	サイトのURLー＞コントローラ・アクション設定
		require_once(self::$CONFIG_DIR . DS . 'routes.php');
		defineRoutes(Router::getRouter());

		//	データベースの接続情報を設定する
		if (Config::get(Config::DATABASE_ENABLED)) {
			require_once(self::$CONFIG_DIR . DS . 'database.php');
			defineDatabases();
		}
	}
}
