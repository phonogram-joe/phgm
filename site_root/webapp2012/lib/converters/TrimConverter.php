<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

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