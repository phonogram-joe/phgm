<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpResponseFormat
{
	//	HTTP応答内容のデータ形
	public static $HTML = 'html';
	public static $JSON = 'json';
	public static $XML = 'xml';
	public static $TEXT = 'txt';
	public static $CSV = 'csv';
	public static $EXCEL_CSV = 'xls.csv';
	public static $HTTP_HEADER_ONLY = 'HTTP';
	public static $FILE = 'FILE';

	private static $MIME_TYPES;
	private static $CHARSETS;
	private static $ENCODINGS;
	private static $DEFAULT_FORMAT;
	private static $IS_INITIALIZED = false;

	public static function classInitialize()
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
		self::$MIME_TYPES[$typeName] = $mimeType;
		self::$CHARSETS[$typeName] = $charset;
		self::$ENCODINGS[$typeName] = $encoding;
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
	 *		HTTPヘッダーで使えるエンコードの略を返す。データ形のMIMEタイプです。
	 */
	public static function mimeType($typeName)
	{
		if (isset(self::$MIME_TYPES[$typeName])) {
			return self::$MIME_TYPES[$typeName];
		}
		throw new Exception('HttpResponseFormat::mimeType() -- 「' . $typeName . '」というフォーマットのMIMEタイプは設定されてない。');
	}

	/*
	 *	charset($charset)
	 *		HTTPヘッダーで使えるエンコードの略を返す応答自体のも文字コードです。
	 */
	public static function encoding($typeName)
	{
		if (isset(self::$ENCODINGS[$typeName])) {
			return self::$ENCODINGS[$typeName];
		}
		throw new Exception('HttpResponseFormat::encoding() -- 「' . $typeName . '」というフォーマットのエンコードは設定されてない。');
	}

	/*
	 *	charset($charset)
	 *		HTTPヘッダーで使えるエンコードの略を返す。Content-type: text/html; charset=UTF-8のメタタグようのcharsetです。
	 */
	public static function charset($typeName)
	{
		if (isset(self::$CHARSETS[$typeName])) {
			return self::$CHARSETS[$typeName];
		}
		throw new Exception('HttpResponseFormat::charset() -- 「' . $typeName . '」というフォーマットのcharsetは設定されてない。');
	}
}
