<?php

class TrimConverter
{
	public static function input($value)
	{
		return trim($value);
	}

	public static function output($value)
	{
		return $value;
	}
}