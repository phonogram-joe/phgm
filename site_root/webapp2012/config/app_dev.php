<?php

//	このシステムの動きに関する設定はここで記入してください。
//	このファイルは＊＊開発環境＊＊のみで有効です

class AppConfig extends AppAllConfig {
}

function appConfigDev()
{
	//	有効のログレベル
	Logger::setLevel(Logger::TRACE);

	//	エラー処理の設定
	ini_set('display_errors', true);
	error_reporting(E_ERROR | E_PARSE | E_DEPRECATED | E_STRICT | E_WARNING);
}
appConfigDev();