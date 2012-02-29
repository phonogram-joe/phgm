<?php

class RequiredValidator
{
	public static function required($value)
	{
		return is_null($value) ? '未入力です' : null;
	}
}