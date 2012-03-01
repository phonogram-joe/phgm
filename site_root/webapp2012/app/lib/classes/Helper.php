<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class Helper
{
	public static function helper()
	{
		return "app/lib/classes/に共通のクラスをおく。このクラスをrequireのにClassLoader::load(HELPER, 'Helper')";
	}
}