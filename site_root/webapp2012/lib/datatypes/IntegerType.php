<?php

class IntegerType extends BaseDataType
{
	const INT_REGEX = '/\d+/';

	public static function fromWeb($value)
	{
		if (is_int($value)) {
			return $value;
		}
		if (preg_match(self::INT_REGEX, $value)) {
			return intval($value);
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