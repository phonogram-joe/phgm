<?php

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