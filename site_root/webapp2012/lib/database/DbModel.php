<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DbModel extends BaseModel
{
	private static $DB_MODELS;
	private static $IS_INITIALIZED = false;

	private static $tableName;
	private static $columns;
	private static $insertSql;

	private function __construct($className) {
		$this->tableName = null;
		$this->columns = array();
		$this->insertSql = null;
	}

	public static function classInitialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$IS_INITIALIZED = true;
		self::$DB_MODELS = array();
	}

	public static function initializeSubclass($className)
	{
		$modelDefinition = BaseModel::getClassModelDefinition($className);
		if (is_null($modelDefinition)) {
			throw new Exception('DbModel::initializeSubclass() -- BaseModel::initializeSubclass()を先にコールしてください。');
		}
		$dbModel = new DbModel($className)
		self::$DB_MODELS[$className] = $dbModel;
		return $dbModel;
	}

	public static function getDbModel($className)
	{
		return self::$DB_MODELS[$className];
	}
}