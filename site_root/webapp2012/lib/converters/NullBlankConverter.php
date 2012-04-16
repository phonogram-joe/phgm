<?php

class NullBlankConverter
{
	public static function input($value)
	{
		return mb_strlen($value) === 0 || mb_strlen(trim($value)) === 0 ? null : trim($value);
	}

	public static function output($value)
	{
		return is_null($value) ? '' : $value;
	}
}