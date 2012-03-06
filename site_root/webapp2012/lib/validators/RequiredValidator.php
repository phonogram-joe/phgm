<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class RequiredValidator
{
	public static function required($value)
	{
		return is_null($value) || strlen(trim($value)) === 0 ? '未入力です。' : null;
	}
}