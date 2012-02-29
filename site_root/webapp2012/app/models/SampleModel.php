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
		$modelDefinition->defineField('name', 'text', array('required'), null, true);
		$modelDefinition->defineField('email', 'email', array('required', 'email'), null, true);
	}

	private function validateSample($value)
	{
		var_dump('validateSample: ' . $value);
		return null;
	}
}