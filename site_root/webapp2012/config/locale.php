<?php
// ローカル設定
setlocale(LC_ALL, 'ja_JP.UTF-8');

// PHP設定
ini_set('output_handler', 'mb_output_handler');
ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.encoding_translation', 'On');
ini_set('mbstring.substitute_character', 'none');

// データベース設定
//	sukatto/config.phpに移動
//	webSupport/config.phpにもある

// PEAR設定
ini_set('include_path', ROOT_DIR . DS . 'vendors' . DS . 'pear' . PS . ini_get('include_path'));

// アプリケーション基本設定
define('DATA_DIR', ROOT_DIR . DS . 'data');

// セッション設定
if(file_exists(ROOT_DIR . DS . 'config' . DS . 'session.php')) {
	require_once(ROOT_DIR . DS . 'config' . DS . 'session.php');
}

if (!defined('SESSION_DIR')) {
	define('SESSION_DIR', ROOT_DIR . DS . 'tmp' . DS . 'session');
}

ini_set('session.save_path', SESSION_DIR);
