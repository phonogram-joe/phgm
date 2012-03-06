<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class EmailValidator
{
	public static function email($value)
	{
		if (preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i', $value)) {
			return null;
		} else {
			return 'メールフォーマットと異なります。';
		}
	}
}