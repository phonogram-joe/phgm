<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class StringUtils
{
	/*
	 *	camelToUnderscores($string)
	 *		'className' => 'class_name'
	 */
	public static function camelToUnderscores($string)
	{
		$toUnderscore = create_function('$c', 'return $c[1] . \'_\' . strtolower($c[2]);');
		return strtolower(preg_replace('/([a-z])([A-Z])/', $toUnderscore, $string));
	}

	/*
	 *	underscoresToCamel($string)
	 *		'class_name' => 'className'
	 */
	public static function underscoresToCamel($string)
	{
		$toCamel = create_function('$c', 'return strtoupper($c[1]);');
    	return preg_replace_callback('/_([a-z])/', $toCamel, $string);	
	}
}