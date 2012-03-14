<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

//	このシステムの動きに関する設定はここで記入してください。
//	このファイルは＊＊全環境＊＊で有効です

function appConfigAll()
{

	//	セッション設定
	ini_set('session.save_path', phgm::$SESSION_DIR);
	Config::set(Config::SESSIONS_ENABLED, true);
	Config::set(Config::SESSION_NAME, 'o293847HJa5bxfqvhb7B86346nk456$8b7kj89878d99fg574qw4g3j4g635KJHYR&KJKHUIY234526214');
	Config::set(Config::SESSIONS_MAX_NONCE_COUNT, 3);
	Config::set(Config::SESSIONS_NONCE_SECRET, '|=dLJAK9d$Pu{CO!X%j/boHtuoYS#;KO3 QCzmDn-|d&*gI:8%yI/MXOSYX.TvUn');

	//	ローカル・文字設定
	setlocale(LC_ALL, 'ja_JP.UTF-8');
	ini_set('date.timezone', 'Asia/Tokyo');
	ini_set('output_handler', 'mb_output_handler');
	ini_set('mbstring.language', 'Japanese');
	ini_set('mbstring.internal_encoding', 'UTF-8');
	ini_set('mbstring.encoding_translation', 'On');
	ini_set('mbstring.substitute_character', 'none');

	//	有効のログレベル
	Logger::setLevel(Logger::TRACE);
	Logger::setFile(phgm::$LOG_DIR . DS . 'system.log');

	//	処理できないエラーの場合に返すメッセージ
	Config::set(Config::FATAL_ERROR_MESSAGE, 'エラーが発生しました。');

	//	エラー処理の設定
	ini_set('display_errors', true);
	error_reporting(E_ERROR | E_PARSE | E_DEPRECATED | E_STRICT | E_WARNING);

	//	有効の環境。設定がこれによって変わる
	Config::set(Config::ENVIRONMENT, Config::ENVIRONMENT_DEVELOPMENT);

	//	Smarty3の設定
	Config::set(Config::SMARTY_LEFT_DELIMITER, '{{');
	Config::set(Config::SMARTY_RIGHT_DELIMITER, '}}');

	//	ブラウザが使えないEDIT・DELETEというHTTPメソッドを再現するように、パラムでメソッドを上書きできる。そのパラム名を設定できる
	Config::set(Config::HTTP_METHOD_PARAM, '__http_method');

	//	HTTP応答のデータ刑とそれに合わせてのMIMEタイプ・エンコードなど
	HttpResponseFormat::registerFormat(HttpResponseFormat::$HTML, 'text/html', 'utf-8', 'UTF8');
	//	HTMLをShift-JISとして返す場合：　
	//		HttpResponseFormat::registerFormat(HttpResponseFormat::$HTML, 'text/html', 'shift_jis', 'SJIS');
	HttpResponseFormat::registerFormat(HttpResponseFormat::$TEXT, 'text/txt', 'utf-8', 'UTF8');
	HttpResponseFormat::registerFormat(HttpResponseFormat::$JSON, 'application/json', 'utf-8', 'UTF8');
	HttpResponseFormat::registerFormat(HttpResponseFormat::$CSV, 'text/csv', 'Shift_JIS', 'SJIS');

	//	デフォルトとして使うデータ刑を設定する
	//	フレームワークのタイプを使う場合は：
	HttpResponseFormat::setDefaultFormat(HttpResponseFormat::$HTML);
	//	例：カスタムなタイプ
	//		define('HTTP_FORMAT_KEITAI_HTML', 'keitai');
	//		HttpResponseFormat::registerFormat(HTTP_FORMAT_KEITAI_HTML, 'text/html', 'shift_jis', 'SJIS');
	//		HttpResponseFormat::setDefaultFormat(HTTP_FORMAT_KEITAI_HTML);


	//	HTTP応答のデータ刑に合わせて、コントローラの変数をそのデータ刑に変換するクラスを設定する
	BaseRenderer::registerRenderer(HttpResponseFormat::$HTML, 'SmartyRenderer', '.html');
	BaseRenderer::registerRenderer(HttpResponseFormat::$TEXT, 'SmartyRenderer', '.tpl');

}
appConfigAll();

