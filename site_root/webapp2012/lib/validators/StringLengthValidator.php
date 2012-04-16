<?php

class StringLengthValidator
{
	public static function string_length($value, $min, $max)
	{
		if (is_null($value)) {
			return null;
		}
		if (mb_strlen($value) < intval($min) || mb_strlen($value) > intval($max)) {
			return $min . '-' . $max . '文字でお願いします';
		}
		return null;
	}
}