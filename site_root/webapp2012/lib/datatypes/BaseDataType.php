<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BaseDataType
{
	private static $IS_INITIALIZED;
	public static $INVALID;

	public static function classInitialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$INVALID = new stdClass();
	}

	public static function fromWeb($value)
	{
		return $value;
	}

	public static function toWeb($value)
	{
		return $value;
	}

	public static function fromDb($value)
	{
		return $value;
	}

	public static function toDb($value)
	{
		return $value;
	}

}