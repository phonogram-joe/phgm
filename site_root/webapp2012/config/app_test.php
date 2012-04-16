<?php

//	このシステムの動きに関する設定はここで記入してください。
//	このファイルは＊＊テスト環境またはユニットテストの場合＊＊のみで有効です

function appConfigTest()
{
	//	有効のログレベル
	Logger::setLevel(Logger::TRACE);

	//	エラー処理の設定
	ini_set('display_errors', true);
	error_reporting(E_ERROR | E_PARSE | E_DEPRECATED | E_STRICT | E_WARNING);
}
appConfigTest();