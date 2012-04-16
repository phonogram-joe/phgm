<?php

class PostcodeValidator
{
	public static function postcode($value)
	{
		if (is_null($value)) {
			return null;
		}
		if (mb_strlen($value) === 0 || preg_match('/^\d{3}-?\d{4}$/', $value)) {
			return null;
		} else {
			return '無効な郵便番号です';
		}
	}
}