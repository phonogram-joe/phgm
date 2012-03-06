<?php

class TimeUtils
{
	private static $NOW = null;

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
}