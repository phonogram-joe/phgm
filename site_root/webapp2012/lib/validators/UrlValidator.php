<?php

class UrlValidator
{
	public static function url($value)
	{
		if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $value)) {
			return null;
		} else {
			return '無効なURLです。';
		}
	}
}