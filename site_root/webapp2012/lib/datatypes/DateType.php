<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DateType extends DatetimeType
{
	public static function toWeb($value)
	{
		if (is_long($value)) {
			return strftime(DatetimeType::FORMAT_STR_DATE, $value);
		} else {
			return $value;
		}
	}

	public static function toDb($value)
	{
		return self::toWeb($value);
	}	
}