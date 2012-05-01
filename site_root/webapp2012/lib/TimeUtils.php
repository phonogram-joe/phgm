<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class TimeUtils
{
	private static $NOW = null;
	const DATE_FORMAT = '%Y-%m-%d %H:%M:%S';
	const DATE_FORMAT_DAY_START = '%Y-%m-%d 00:00:00';
	const SECONDS_IN_DAY = 86400; //24 * 60 * 60

	public static function classInitialize()
	{
		self::$NOW = time();
	}

	//テスト用
	public static function setTime($time)
	{
		self::$NOW = $time;
	}

	public static function now()
	{
		return self::$NOW;
	}

	public static function startOfDay($datetime)
	{
		return strtotime('midnight', $datetime);
	}

	public static function startOfNextDay($datetime)
	{
		return strtotime('midnight tomorrow', $datetime);
	}
}