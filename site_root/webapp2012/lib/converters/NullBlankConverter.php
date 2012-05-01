<?php

class NullBlankConverter
{
	public static function input($value)
	{
		if (is_string($value)) {
			return mb_strlen($value) === 0 || mb_strlen(trim($value)) === 0 ? null : trim($value);
		} elseif (is_array($value) && count($value) === 0) {
			return null;
		} else {
			return $value;
		}
	}

	public static function output($value)
	{
		return is_null($value) ? '' : $value;
	}
}