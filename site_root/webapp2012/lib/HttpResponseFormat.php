<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpResponseFormat
{
	//	HTTP応答内容のデータ刑
	public static $HTML = 'html';
	public static $JSON = 'json';
	public static $XML = 'xml';
	public static $TEXT = 'txt';
	public static $CSV = 'csv';
	public static $EXCEL_CSV = 'xls.csv';
	public static $HTTP_HEADER_ONLY = 'HTTP';

	public static function mimeFor($type)
	{
		
	}

	/*
	 *	charset($charset)
	 *		HTTPヘッダーで使えるエンコードの略を返す。ナルの場合はmb_http_output()のエンコードを使う。
	 */
	public static function charset($charset)
	{
		if (is_null($charset)) {
			$charset = mb_http_output();
		}
		switch ($charset) {
			case 'SJIS':
				return 'shift_jis';
				break;
			case 'UTF8':
				return 'utf-8';
				break;
			default:
				return $charset;
				break;
		}
	}
}