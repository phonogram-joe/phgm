<?php

//	このシステムの動きに関する設定はここで記入してください。
//	このファイルは＊＊全環境＊＊で有効です

//	セッション設定
ini_set('session.save_path', SESSION_DIR);
define('SESSION_FLASH_KEY', 'jp.phonogram.session.flash');
define('SESSION_FLASHES_KEY', 'jp.phonogram.session.flashes');

//	PEAR設定
ini_set('include_path', ROOT_DIR . DS . 'vendors' . DS . 'pear' . PS . ini_get('include_path'));

//	ローカル・文字設定
setlocale(LC_ALL, 'ja_JP.UTF-8');
ini_set('output_handler', 'mb_output_handler');
ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.encoding_translation', 'On');
ini_set('mbstring.substitute_character', 'none');

//	有効のログレベル
define('LOGGER_LEVEL', LOG_WARN);
define('LOGGER_FILE', LOG_DIR . DS . 'system.log');

//	環境のオプション
define('ENVIRONMENT_TEST', 'test');
define('ENVIRONMENT_DEVELOPMENT', 'dev');
define('ENVIRONMENT_PRODUCTION', 'prod');

//	有効の環境。設定がこれによって変わる
define('ENVIRONMENT', ENVIRONMENT_DEVELOPMENT);

//	HTTP応答のデータ刑とそれに合わせてのMIMEタイプ・エンコードなど
HttpResponseFormat::registerFormat(HttpResponseFormat::$HTML, 'text/html', 'utf-8', 'UTF8');
HttpResponseFormat::registerFormat(HttpResponseFormat::$TEXT, 'text/txt', 'utf-8', 'UTF8');
HttpResponseFormat::registerFormat(HttpResponseFormat::$JSON, 'application/json', 'utf-8', 'UTF8');
HttpResponseFormat::registerFormat(HttpResponseFormat::$CSV, 'text/csv', 'Shift_JIS', 'SJIS');
