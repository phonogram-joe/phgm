<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DecimalType extends BaseDataType
{
	const DECIMAL_REGEX = '/^([-]?)\d+(\.\d+)?$/';

	public static function fromWeb($value)
	{
		if (is_float($value)) {
			return $value;
		}
		if (preg_match(self::DECIMAL_REGEX, $value)) {
			return floatval($value);
		}
		return BaseDataType::$INVALID;
	}

	public static function toWeb($value)
	{
		return strval($value);
	}

	public static function fromDb($value)
	{
		return self::fromWeb($value);
	}

	public static function toDb($value)
	{
		return self::toWeb($value);
	}
}