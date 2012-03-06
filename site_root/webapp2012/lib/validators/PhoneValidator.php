<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class PhoneValidator
{
	public static function phone($value)
	{
		if (preg_match('/^\d{1,5}-?\d{1,5}-?\d{1,5}$/', $value)) {
			return null;
		} else {
			return '無効な電話番号です。';
		}
	}
}