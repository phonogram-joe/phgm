<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

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