<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BooleanType extends BaseDataType
{
	public static function fromWeb($value)
	{
		if (is_bool($value)) {
			return $value;
		}
		if ($value === "1") {
			return true;
		} else if ($value === "0") {
			return false;
		} else {
			return BaseDataType::$INVALID;
		}
	}

	public static function toWeb($value)
	{
		return $value === true ? "1" : "0";
	}

	public static function fromDb($value)
	{
		if ($value === "1" || $value === true || $value === 1) {
			return true;
		} else {
			return false;
		}
	}

	public static function toDb($value)
	{
		return $value;
	}
}