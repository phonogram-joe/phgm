<?php

//	このシステムの動きに関する設定はここで記入してください。
//	このファイルは＊＊公開環境＊＊のみで有効です

function appConfigProd()
{
	//	有効のログレベル
	Logger::setLevel(Logger::ERROR);

	//	エラー処理の設定
	ini_set('display_errors', false);
	error_reporting(0);
}
appConfigProd();