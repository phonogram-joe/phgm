<?php
/*
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 *
 *	bootstrap.php
 *		フレームワークの必要なファイルをrequireして、初期化する
 */

//	リクエストの開始時のタイムスタンプ
define('REQUEST_TIME', time());

//	ショートカット
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

//	webapp2012フレームワークの基本のフォルダー
define('ROOT_DIR', dirname(__FILE__));
define('APP_DIR', ROOT_DIR . DS . 'app');
define('CONFIG_DIR', ROOT_DIR . DS . 'config');
define('DATA_DIR', ROOT_DIR . DS . 'data');
define('LIB_DIR', ROOT_DIR . DS . 'lib');
define('VENDOR_DIR', ROOT_DIR . DS . 'lib-vendor');
define('LOG_DIR', ROOT_DIR . DS . 'log');
define('TMP_DIR', ROOT_DIR . DS . 'tmp');

//	一時的なファイルのパス
define('DB_CACHE_DIR', TMP_DIR . DS . 'db_cache'); //
define('DATA_CACHE_DIR', TMP_DIR . DS . 'data_cache'); //
define('SESSION_DIR', TMP_DIR . DS . 'session'); //
define('TEMPLATES_COMPILE_DIR', TMP_DIR . DS . 'templates_compile'); //
define('TEMPLATES_CACHE_DIR', TMP_DIR . DS . 'templates_cache'); //

//	ログレベルのオプション
define('LOG_TRACE', 1);
define('LOG_DEBUG', 2);
define('LOG_INFO', 3);
define('LOG_WARN', 4);
define('LOG_ERROR', 5);
define('LOG_FATAL', 6);

//	このapp専用のファイル
define('CONTROLLERS_DIR', APP_DIR . DS . 'controllers'); //コントローラクラス
define('APP_LIB_DIR', APP_DIR . DS . 'lib'); //共通のクラスやビュープラグイン
define('MODELS_DIR', APP_DIR . DS . 'models'); //DBやフォームのモデルクラス
define('DECORATORS_DIR', APP_DIR . DS . 'view_models'); //モデルを表示する際に使うクラス
define('VIEWS_DIR', APP_DIR . DS . 'views'); //共通のレイアウト・ガジェットと、コントローラのアクションごとのテンプレート
define('VALIDATORS_DIR', APP_LIB_DIR . DS . 'validators'); //モデルのデータがデータ刑や規則にあってるか確認するクラス。
define('RENDERERS_DIR', APP_LIB_DIR . DS . 'renderers'); //カスタムなレンダラクラス。Smartyのようにデータをテンプレートに埋め込むクラスです。

//	ライブラリーのクラス
require_once(LIB_DIR . DS . 'Logger.php');
require_once(LIB_DIR . DS . 'StringUtils.php');
require_once(LIB_DIR . DS . 'ModelDefinition.php');
require_once(LIB_DIR . DS . 'BaseModel.php');
require_once(LIB_DIR . DS . 'BaseDecorator.php');
require_once(LIB_DIR . DS . 'BaseController.php');
require_once(LIB_DIR . DS . 'BaseRenderer.php');
require_once(LIB_DIR . DS . 'Router.php');
require_once(LIB_DIR . DS . 'ClassLoader.php');
require_once(LIB_DIR . DS . 'HttpRequest.php');
require_once(LIB_DIR . DS . 'HttpResponse.php');
require_once(LIB_DIR . DS . 'HttpResponseFormat.php');
require_once(LIB_DIR . DS . 'HttpHandler.php');

//	ライブラリーの初期化
BaseRenderer::classInitialize();
HttpResponseFormat::classInitialize();
BaseModel::classInitialize();

//	vendorのクラス
//	...none...

//	サイトの動きに関する設定
require_once(CONFIG_DIR . DS . 'app_all.php'); //appの設定
if (ENVIRONMENT === ENVIRONMENT_PRODUCTION) {
	require_once(CONFIG_DIR . DS . 'app_' . ENVIRONMENT_PRODUCTION . '.php'); //公開環境のみの設定
} else if (ENVIRONMENT === ENVIRONMENT_DEVELOPMENT) {
	require_once(CONFIG_DIR . DS . 'app_' . ENVIRONMENT_DEVELOPMENT . '.php'); //開発環境のみの設定
} else if (ENVIRONMENT === ENVIRONMENT_TEST) {
	require_once(CONFIG_DIR . DS . 'app_' . ENVIRONMENT_TEST . '.php'); //開発環境のみの設定
}
//	サイトのURLー＞コントローラ・アクション設定
require_once(CONFIG_DIR . DS . 'routes.php');
