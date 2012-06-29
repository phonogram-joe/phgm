<?php

//	ログイン情報を代表するフォーム　（DBと連携しない）
class LoginForm extends BaseModel
{
	public static $MODEL_DEFINITION;

	public static function classInitialize()
	{
		$modelDefinition = BaseModel::initializeSubclass(__CLASS__);
		if (is_null($modelDefinition)) {
			return;
		}
		$modelDefinition->defineField('login_id', 'ログインID　（メール）', 'string', array('required', 'email'), array('null_blank'), true);
	}

	public static function create()
	{
		return new LoginForm();
	}
}