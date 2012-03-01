<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SampleModel extends BaseModel
{
	public static $MODEL_DEFINITION;

	public static function classInitialize()
	{
		$modelDefinition = BaseModel::initializeSubclass(__CLASS__);
		$modelDefinition->defineField('name', '名前', 'text', array('required', 'validateNonEmpty'), null, true);
		$modelDefinition->defineField('email', 'Eメール', 'email', array('required', 'email'), null, true);
	}

	public static function validateNonEmpty($value)
	{
		return is_null($value) || strlen(trim($value)) === 0 ? '未入力です' : null;
	}
}