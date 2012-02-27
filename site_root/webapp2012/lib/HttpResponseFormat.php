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

	private static $MIME_TYPES;
	private static $CHARSETS;
	private static $ENCODINGS;
	private static $DEFAULT_FORMAT;
	private static $IS_INITIALIZED = false;

	public static function initialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$IS_INITIALIZED = true;

		self::$MIME_TYPES = array();
		self::$CHARSETS = array();
		self::$ENCODINGS = array();

		self::$DEFAULT_FORMAT = self::$HTML;
	}

	public static function registerFormat($typeName, $mimeType, $charset, $encoding)
	{
		self::$MIME_TYPES[$name] = $mimeType;
		self::$CHARSETS[$name] = $charset;
		self::$ENCODINGS[$name] = $encoding;
	}

	public static function setDefaultFormat($typeName)
	{
		self::$DEFAULT_FORMAT = $typeName;
	}

	public static function getDefaultFormat()
	{
		return self::$DEFAULT_FORMAT;
	}

	/*
	 *	mimeType($type)
	 *		HTTPヘッダーで使えるエンコードの略を返す。ナルの場合はmb_http_output()のエンコードを使う。
	 */
	public static function mimeType($typeName)
	{
		return self::$MIME_TYPES[$typeName];
	}

	/*
	 *	charset($charset)
	 *		HTTPヘッダーで使えるエンコードの略を返す。ナルの場合はmb_http_output()のエンコードを使う。
	 */
	public static function encoding($typeName)
	{
		return self::$ENCODINGS[$typeName];
	}

	/*
	 *	charset($charset)
	 *		HTTPヘッダーで使えるエンコードの略を返す。ナルの場合はmb_http_output()のエンコードを使う。
	 */
	public static function charset($typeName)
	{
		if (is_null($charset)) {
			$charset = mb_http_output();
		}
		return self::$CHARSETS[$typeName];
	}
}
