<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class FirstArrayKeyIntType extends IntegerType
{
	public static function fromWeb($value)
	{
		if (is_array($value)) {
			$value = array_keys($value);
			$value = count($value) > 0 ? $value[0] : null;
		}
		return parent::fromWeb($value);
	}
}