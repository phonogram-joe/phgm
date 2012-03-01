<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DatabaseSession
{
	private static $INSERT = 'insert';
	private static $UPDATE = 'update';

	private $dbHandle;
	private $changedObjects;
	private $newObjects;
	private $statements;

	public function DatabaseSession($dbHandle)
	{
		$this->dbHandle = $dbHandle;
		$this->changedObjects = array();
		$this->newObjects = array();
		$this->statements = array();
	}

	/*
	public function query($className, $where, $params, $order = null, $limit = null, $page = null)
	{
		$table = DbModel::getTableName($className);
		$columns = implode(',', DbModel::getColumns($className));
		$commonWhere = DbModel::getCommonConditions($className);
		$sql  = 'SELECT ' . $columns;
		$sql .= ' FROM ' . $table;
		$sql .= ' WHERE (' . $params . ')';
		//$sql .= ' AND (' . $commonWhere . ')';
		//$sql .= ' LIMIT ?' . $limit;
		//$sql .= ' OFFSET ?' . $page * $limit;
		//$sql .= ' ORDER BY ' . $order;

		$statement = $this->dbHandle->prepare($sql);
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$results = array();
		$result;
		while ($result = $statement->fetch()) {
			$results[] = $results;
		}
		return $results;
	}

	public function trackChanged($object)
	{
		$this->changedObjects[] = $object;
	}

	public function trackNew($object)
	{
		$this->newObjects[] = $object;
	}

	public function flush()
	{
		try {
			$this->dbHandle->beginTransaction();

			foreach ($this->changedObjects as $object) {
				//save the changes to the objects
			}
			foreach ($this->newObjects as $object) {
				//add the objects
			}

			$this->dbHandle->commit();
		} catch (Exception $e) {
			$this->dbHandle->rollback();
			throw $e;
		}
	}

	private function insertObject($object)
	{
		$className = get_class($object);
		$statementName = self::$INSERT . '::' . $className;
		if (false === array_key_exists($statementName, $this->statements)) {
			$statement = $this->statements[$statementName];
		} else {
			$table = DbModel::getTableName($className);
			$columns = implode(',', DbModel::getColumns($className));
			$sql  = 'INSERT INTO ' . $table;
			$sql .= ' (' . implode(',', $columns) . ')';
			$sql .= ' VALUES (' . get_object_vars($object) ;
			$statement = $this->dbHandle->prepare()
		}
	}

	private function updateObject($object)
	{
		
	}

	private function cleanupFlush()
	{
		$this->changedObjects = array();
		$this->newObjects = array();
	}
	*/
}