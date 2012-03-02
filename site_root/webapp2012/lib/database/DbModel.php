<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DbModel extends BaseModel
{
	private static $DB_MODELS;
	private static $IS_INITIALIZED = false;

	private $tableName;
	private $idName;
	private $columns;
	private $insertSql;
	private $selectSql;

	private function __construct($className, $modelDefinition) {
		$this->tableName = null;
		$this->idName = 'id';
		$this->columns = array();
		$this->insertSql = null;
		$this->selectSql = null;

		$fields = $modelDefinition->getFields();
		foreach ($fields as $name => $field) {
			$this->columns[] = $name;
		}
		$this->setTableName(ClassLoader::classNamePrefix($className)); // AdminUserModel -> admin_user
	}

	private function createInsertSql()
	{
		Logger::info('DbModel:createInsertSql() -- creating insert SQL for table: ' . $this->tableName);
		$sql = 'INSERT INTO ' . $this->tableName;
		$sql .= ' (' . implode(', ', $this->columns) . ')';
		$sql .= ' VALUES (' . implode(',', preg_split('//', str_repeat('?', count($this->columns)), -1, PREG_SPLIT_NO_EMPTY)) . ')';
		Logger::info('DbModel:createInsertSql() -- creating insert SQL for table: ' . $this->tableName . ': ' . $sql);
		$this->insertSql = $sql;
	}

	private function createSelectSql()
	{
		$sql = 'SELECT ' . $this->idName . ', ' . implode(', ', $this->columns);
		$sql .= ' FROM ' . $this->tableName;
		$this->selectSql = $sql;
	}

	public function setTableName($tableName)
	{
		$this->tableName = $tableName;
		$this->createInsertSql();
		$this->createSelectSql();
	}
	public function getTableName()
	{
		return $this->tableName;
	}
	public function setIdName($idName)
	{
		$this->idName = $idName;
		$this->createSelectSql();
	}
	public function getIdName()
	{
		return $this->idName;
	}

	public function setId($object, $id)
	{
		$object->{$this->idName} = $id;
	}

	public function getSelectSql()
	{
		return $this->selectSql;
	}

	public function getInsert($object)
	{
		$insert = array('sql' => $this->insertSql);
		$data = array();
		foreach ($this->columns as $column) {
			$data[] = $object->get($column);
		}
		$insert['data'] = $data;
		Logger::info('DbModel:getInsert() -- sql: ' . $insert['sql']);
		return $insert;
	}

	public function getUpdate($object, $forceUpdate = false)
	{
		$data = array();
		$set = array();
		foreach ($this->columns as $column) {
			if ($object->isChanged($column) || $forceUpdate) {
				$set[] = ' ' . $column . ' = ? ';
				$data[] = $object->get($column);
			}
		}
		if (count($data) === 0) {
			return null; //UPDATEは不要：何も変わってない
		}
		$sql = 'UPDATE ' . $this->tableName;
		$sql .= ' SET ' . implode(', ', $set);
		$sql .= ' WHERE ' . $this->idName . ' = ?';
		$data[] = $object->get($this->idName);
		return array('sql' => $sql, 'data' => $data);
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
		$dbModel = new DbModel($className, $modelDefinition);
		self::$DB_MODELS[$className] = $dbModel;
		return $dbModel;
	}

	public static function getDbModel($className)
	{
		return self::$DB_MODELS[$className];
	}

	public static function hasId($object)
	{
		$dbModel = self::getDbModel(get_class($object));
		return isset($object->{$dbModel->getIdName()});
	}
}