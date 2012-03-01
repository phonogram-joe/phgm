<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SampleValidator
{
	/*
	 *	バリデーションをするメソッドはクラス名と同じにします。
	 *
	 *	例：
	 *		SampleValidator -> sample()
	 *		PhoneValidator -> phone()
	 *
	 *	@value: バリデートする価値
	 *	@returns: OKの場合はnull、ダメな場合はエラーメッセージ
	 */
	public static function sample($value)
	{
		if ($value == 'sample') {
			return null; //OK
		} else {
			return '「sample」と異なります。';
		}
	}
}