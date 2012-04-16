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
		$modelDefinition->defineField('name', '名前', 'string', array('required', 'validateNonEmpty'), null, true);
		$modelDefinition->defineField('email', 'Eメール', 'email', array('required', 'email'), null, true);
		$dbModel = DbModel::initializeSubclass(__CLASS__);
		$dbModel->setTableName('sample'); //デフォルトで「Class」を抜いたクラス名になるので、SampleModelはsampleになります。例として入れています。
		$dbModel->setIdName('id'); //デフォルトのidなので、不要です。例として入れています。
	}

	public static function validateNonEmpty($value)
	{
		return is_null($value) || strlen(trim($value)) === 0 ? '未入力です' : null;
	}
}