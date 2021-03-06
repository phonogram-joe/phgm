<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class UrlValidator
{
	public static function url($value)
	{
		if (is_null($value)) {
			return null;
		}
		if (mb_strlen($value) === 0 || preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $value)) {
			return null;
		} else {
			return '無効なURLです。';
		}
	}
}