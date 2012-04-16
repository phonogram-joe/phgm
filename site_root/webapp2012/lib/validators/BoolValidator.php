<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BoolValidator
{
	public static function bool($value)
	{
		if (is_null($value) || mb_strlen($value) === 0) {
			return null;
		}
		if ($value === "0" || $value === "1") {
			return null;
		} else {
			return 'ブールではありません';
		}
	}
}