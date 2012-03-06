<?php

class PostcodeValidator
{
	public static function postcode($value)
	{
		if (preg_match('/^\d{3}-?\d{4}$/', $value)) {
			return null;
		} else {
			return '無効な郵便番号です。';
		}
	}
}