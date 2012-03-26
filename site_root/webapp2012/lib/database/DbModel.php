<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DbModel extends BaseModel
{
	private static $DB_MODELS;
	private static $IS_INITIALIZED = false;

	const BEFORE_INSERT = 0; //INSERTのみ
	const BEFORE_UPDATE = 1; //UPDATEのみ
	const BEFORE_SAVE = 2; //INSERTとUPDATEの両方
	const BEFORE_DELETE = 3; //DELETEのみ

	private $tableName;
	private $idName;
	private $columns;
	private $insertSql;
	private $selectSql;
	private $callbacks;

	private function __construct($className, $modelDefinition) {
		$this->tableName = null;
		$this->idName = 'id';
		$this->columns = array();
		$this->insertSql = null;
		$this->selectSql = null;
		$this->callbacks = array();

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
		$values = array();
		foreach ($this->columns as $column) {
			$values[] = ':' . $column;
		}
		$sql .= ' VALUES (' . implode(',', $values) . ')';
		Logger::info('DbModel:createInsertSql() -- creating insert SQL for table: ' . $this->tableName . ': ' . $sql);
		$this->insertSql = $sql;
	}

	private function createSelectSql()
	{
		$sql = 'SELECT ' . $this->idName . ', ' . implode(', ', $this->columns);
		$sql .= ' FROM ' . $this->tableName;
		$this->selectSql = $sql;
	}

	public function addCallback($type, $methodName)
	{
		if (!array_search($type, array(self::BEFORE_DELETE, self::BEFORE_UPDATE, self::BEFORE_INSERT, self::BEFORE_SAVE))) {
			throw new Exception('DbModel:addCallback() -- コールバックコードは無効です。');
		}
		$this->callbacks[$type] = $methodName;
	}

	public function doCallback($type, $object)
	{
		if (!array_key_exists($type, $this->callbacks)) {
			return;
		}
		$callback = $this->callbacks[$type];
		call_user_func(array($object, $callback));
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

	public function setNonDbColumn($field)
	{
		$columnIndex = array_search($field, $this->columns);
		if (false === $columnIndex) {
			throw new Exception('DbModel:setNonDbColumn() -- ' . $this->tableName . 'には' . $field . 'というコラムはありません。');
		}
		unset($this->columns[$columnIndex]);
		$this->createInsertSql();
		$this->createSelectSql();
	}

	public function setId($object, $id)
	{
		$object->{$this->idName} = $id;
	}

	public function getSelectSql()
	{
		return new SqlStatement($this->selectSql, array());
	}

	public function getInsert($object)
	{
		$data = array();
		foreach ($this->columns as $column) {
			$data[$column] = $object->get($column);
		}
		return new SqlStatement($this->insertSql, $data);
	}

	public function getUpdate($object, $forceUpdate = false)
	{
		$data = array();
		$set = array();
		foreach ($this->columns as $column) {
			if ($object->isChanged($column) || $forceUpdate) {
				$set[] = ' ' . $column . ' = :' . $column;
				$data[$column] = $object->get($column);
			}
		}
		if (count($data) === 0) {
			return null; //UPDATEは不要：何も変わってない
		}
		$sql = 'UPDATE ' . $this->tableName;
		$sql .= ' SET ' . implode(', ', $set);
		$sql .= ' WHERE ' . $this->idName . ' = :' . $this->idName;
		$data[$this->idName] = $object->get($this->idName);
		return new SqlStatement($sql, $data);
	}

	public function getDelete($object)
	{
		$sql = 'DELETE FROM ' . $this->tableName;
		$sql .= ' WHERE ' . $this->idName . ' = ?';
		$data = array($object->get($this->idName));
		return new SqlStatement($sql, $data);
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
		if (!isset(self::$DB_MODELS[$className])) {
			return null;
		}
		return self::$DB_MODELS[$className];
	}

	public static function hasId($object)
	{
		$dbModel = self::getDbModel(get_class($object));
		return isset($object->{$dbModel->getIdName()});
	}

	public static function datetime($epoch)
	{
		return date('Y-m-d H:i:s', $epoch);
	}
}