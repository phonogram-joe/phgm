<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DatabaseSession
{
	private $dbHandle;
	private $trackedObjects;

	public function DatabaseSession($dbHandle)
	{
		$this->dbHandle = $dbHandle;
		$this->trackedObjects = array();
	}

	public function findBy($className, $column, $value)
	{
		$table = DbModel::getDbModel($className);
		$sql = $table->getSelectSql();
		$sql .= ' WHERE ' . $column . ' = ?';
		$sql .= ' LIMIT 1';
		$data = array($value);
		Logger::info('DatabaseSession:query -- ' . $sql);
		$statement = $this->dbHandle->prepare($sql); // '->query()'かな？
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$result = $statement->execute($data);
		$result = $statement->fetch();
		$statement->closeCursor();
		return $result !== false ? $result : null;
	}

	public function find($className, $id)
	{
		$table = DbModel::getDbModel($className);
		$sql = $table->getSelectSql();
		$sql .= ' WHERE ' . $table->getIdName() . ' = ?';
		$data = array($id);
		Logger::info('DatabaseSession:query -- ' . $sql);
		$statement = $this->dbHandle->prepare($sql); // '->query()'かな？
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$result = $statement->execute($data);
		$result = $statement->fetch();
		$statement->closeCursor();
		return $result !== false ? $result : null;
	}

	/*
	 *	query($className, $conditions, $values)
	 *		クエリを行って結果を返す。
	 *
	 *	@className: String - 結果をどのクラスに変換するか設定する。例： 'SampleModel'
	 *	@conditions: String - クエリの条件。例：　'name = :name OR email = :email'
	 *	@values: Array - クエリの条件に合わせたデータ。例： array('name' => '田中', 'email' => 'tanaka@example.com')
	 *	
	 *	@returns: Array. array('sql' => 'SQLストリング', 'data' => array(...)
	 *	上記の例で、array('sql' => 'name = ? or email = ?', 'data' => array('田中', 'tanaka@example.com'))
	 */
	public function query($className, $conditions = null, $values = null)
	{
		$table = DbModel::getDbModel($className);
		$sql = $table->getSelectSql();
		if (!is_null($conditions)) {
			$where = new WhereClause($conditions, $values);
			$sql .= $where->getSql();
			$data = $where->getData();
		} else {
			$data = array();
		}
		Logger::info('DatabaseSession:query -- ' . $sql);
		$statement = $this->dbHandle->prepare($sql); // '->query()'かな？
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$result = $statement->execute($data);
		$results = $statement->fetchAll();
		$statement->closeCursor();
		return $results !== false && is_array($results) && count($results) > 0 ? $results : null;
	}

	public function track($object)
	{
		if (!in_array($object, $this->trackedObjects, true)) {
			Logger::trace('DatabaseSession:track() -- adding ' . $object . ' to tracking.');
			$this->trackedObjects[] = $object;
		} else {
			Logger::trace('DatabaseSession:track() -- object ' . $object . ' already being tracked');
		}
	}

	public function flush()
	{
		if (count($this->trackedObjects) === 0) {
			return null;
		}
		try {
			Logger::trace('DatabaseSession:flush() -- beginTransaction');
			$this->dbHandle->beginTransaction();

			foreach ($this->trackedObjects as $object) {
				if (!$object->isValid()) {
					throw new Exception('DatabaseSession:flush() -- オブジェクトは有効ではありません。');
				}
				if (DbModel::hasId($object)) {
					Logger::trace('DatabaseSession:flush() -- object has id, updating');
					$this->updateObject($object);
				} else {
					Logger::trace('DatabaseSession:flush() -- object does NOT have id, inserting');
					$this->insertObject($object);
				}
			}

			Logger::trace('DatabaseSession:flush() -- commit');
			$this->dbHandle->commit();
			$this->cleanupFlush();
			return true;
		} catch (Exception $e) {
			$this->dbHandle->rollback();
			Logger::trace('DatabaseSession:flush() -- rollback');
			throw $e;
			return false;
		}
	}

	private function insertObject($object)
	{
		$dbModel = DbModel::getDbModel(get_class($object));
		$insert = $dbModel->getInsert($object);
		$sql = $insert['sql'];
		$data = $insert['data'];
		Logger::info('DatabaseSession:query -- insert class ' . get_class($object) . ' with: ' . $sql);
		$statement = $this->dbHandle->prepare($sql);
		$statement->execute($data);
		$dbModel->setId($object, $this->dbHandle->lastInsertId());
	}

	private function updateObject($object)
	{
		$dbModel = DbModel::getDbModel(get_class($object));
		$update = $dbModel->getUpdate($object);
		if (is_null($update)) {
			//オブジェクトが変わってない
			return;
		}
		$sql = $update['sql'];
		$data = $update['data'];
		Logger::info('DatabaseSession:query -- update class ' . get_class($object) . ' with; ' . $sql);
		$statement = $this->dbHandle->prepare($sql);
		$statement->execute($data);
		return $statement->rowCount();
	}

	private function cleanupFlush()
	{
		foreach ($this->trackedObjects as $object) {
			$object->resetChanges();
		}
		$this->trackedObjects = array();
	}
}