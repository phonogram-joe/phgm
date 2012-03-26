<?php

class KanaConverter
{
	public static function input($value, $kanaOptions)
	{
		return mb_convert_kana($value, $kanaOptions);
	}

	public static function output($value)
	{
		return $value;
	}
}