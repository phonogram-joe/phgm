<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class IntValidator
{
	public static function int($value)
	{
		if (is_null($value)) {
			return null;
		}
		if (mb_strlen($value) === 0 || is_int(intval($value))) {
			return null;
		} else {
			return '整数ではありません。';
		}
	}
}