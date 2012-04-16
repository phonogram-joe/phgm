<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DatetimeValidator
{
	public static function datetime($value)
	{
		if (is_null($value) || mb_strlen($value) === 0) {
			return null;
		}
		$datetime = strtotime($value);
		if (false === $datetime || -1 === $datetime) {
			return '無効な日時です';
		} else {
			return null;
		}
	}
}