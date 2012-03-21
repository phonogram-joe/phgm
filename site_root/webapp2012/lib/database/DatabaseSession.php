<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DatabaseSession
{
	private $dbHandle;
	private $allowUpdates;
	private $trackedObjects;
	private $deletedObjects;

	public function DatabaseSession($dbHandle)
	{
		$this->dbHandle = $dbHandle;
		$this->allowUpdates = false;
		$this->trackedObjects = array();
		$this->deletedObjects = array();
	}

	public function setAllowUpdates($allow)
	{
		if (!is_bool($allow)) {
			throw new Exception('DatabaseSession:setAllowUpdates() -- ブールを指定ください。');
		}
		$this->allowUpdates = $allow;
	}

	public function getAllowUpdates()
	{
		return $this->allowUpdates;
	}

	public function findOneBy($className, $column, $value)
	{
		$table = DbModel::getDbModel($className);
		$sql = $table->getSelectSql();
		$sql .= ' WHERE ' . $column . ' = ?';
		$sql .= ' LIMIT 1';
		$data = array($value);
		Logger::info('DatabaseSession:query -- << ' . $sql . ' >> using ' . implode(', ', $data));
		$statement = $this->dbHandle->prepare($sql);
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$result = $statement->execute($data);
		$result = $statement->fetch();
		$statement->closeCursor();
		return $result !== false ? $result : null;
	}

	public function findOne($className, $id)
	{
		$table = DbModel::getDbModel($className);
		$sql = $table->getSelectSql();
		$sql .= ' WHERE ' . $table->getIdName() . ' = ?';
		$data = array($id);
		Logger::info('DatabaseSession:query -- << ' . $sql . ' >> using ' . implode(', ', $data));
		$statement = $this->dbHandle->prepare($sql);
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$result = $statement->execute($data);
		$result = $statement->fetch();
		$statement->closeCursor();
		return $result !== false ? $result : null;
	}

	public function findAllJoin($baseClassName, $joinArray, $conditions, $values, $perPage = null, $pageIndex = null, $orderBy = null)
	{
		$join = new JoinStatement($baseClassName, $joinArray);
		$sql = $join->getSql();
		$where = new WhereClause($conditions, $values);
		$sql .= $where->getSql();
		$data = $where->getData();
		if (!is_null($orderBy)) {
			$sql .= ' ORDER BY ' . $orderBy;
		}
		if (!is_null($perPage)) {
			$sql .= ' LIMIT ' . intval($perPage);
			if (!is_null($pageIndex) && intval($pageIndex) > 0) {
				$sql .= ' OFFSET ' . (intval($perPage) * intval($pageIndex));
			}
		}
		Logger::info('DatabaseSession:query -- << ' . $sql . ' >> using ' . implode(', ', $data));
		$statement = $this->dbHandle->prepare($sql);
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$result = $statement->execute($data);
		$results = $statement->fetchAll();
		if ($results === false || !is_array($results) || count($results) <= 0) {
			Logger::info('DatabaseSession:findAllJoin() -- no resuls.');
			return null;
		}
		$results = $join->processResults($results);
		$statement->closeCursor();
		return $results;
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
	public function findAll($className, $conditions, $values, $perPage = null, $pageIndex = null, $orderBy = null)
	{
		$table = DbModel::getDbModel($className);
		$sql = $table->getSelectSql();
		$where = new WhereClause($conditions, $values);
		$sql .= $where->getSql();
		$data = $where->getData();
		if (!is_null($orderBy)) {
			$sql .= ' ORDER BY ' . $orderBy;
		}
		if (!is_null($perPage)) {
			$sql .= ' LIMIT ' . intval($perPage);
			if (!is_null($pageIndex) && intval($pageIndex) > 0) {
				$sql .= ' OFFSET ' . (intval($perPage) * intval($pageIndex));
			}
		}
		Logger::info('DatabaseSession:query -- << ' . $sql . ' >> using ' . implode(', ', $data));
		$statement = $this->dbHandle->prepare($sql);
		$statement->setFetchMode(PDO::FETCH_CLASS, $className);
		$result = $statement->execute($data);
		$results = $statement->fetchAll();
		$statement->closeCursor();
		return $results !== false && is_array($results) && count($results) > 0 ? $results : null;
	}

	public function track($object)
	{
		if (!in_array($object, $this->deletedObjects, true)) {
			if (!in_array($object, $this->trackedObjects, true)) {
				Logger::trace('DatabaseSession:track() -- tracking ' . $object);
				$this->trackedObjects[] = $object;
			}
		} else {
			throw new Exception('DatabaseSession:track() -- オブジェクトの削除リストに追加(delete)されています。');
		}
	}
	public function delete($object)
	{
		if (!in_array($object, $this->trackedObjects, true)) {
			if (!in_array($object, $this->deletedObjects, true)) {
				Logger::trace('DatabaseSession:delete() -- deleting ' . $object);
				$this->deletedObjects[] = $object;
			}
		} else {
			throw new Exception('DatabaseSession:delete() -- オブジェクトは変更・登録リストに追加(track)されています。');
		}
	}

	public function flush()
	{
		if (!$this->allowUpdates) {
			//	DBに書き込むする場合はHTTPのGETリクエストは非常危険なので、POST・PUT・DELETEHTTPメソッドを使ってください。
			throw new Exception('DatabaseSession:flush() -- リクエストの形によりDBの書き込みは禁止されています。');
		}
		if (count($this->trackedObjects) === 0 && count($this->deletedObjects) === 0) {
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
			foreach ($this->deletedObjects as $object) {
				if (DbModel::hasId($object)) {
					$this->deleteObject($object);
				} else {
					throw new Exception('DatabaseSession:flush() -- object does NOT have id, cannot delete');
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
		$dbModel->doCallback(DbModel::BEFORE_INSERT, $object);
		$dbModel->doCallback(DbModel::BEFORE_SAVE, $object);
		$insert = $dbModel->getInsert($object);
		$sql = $insert['sql'];
		$data = $insert['data'];
		Logger::info('DatabaseSession:query -- insert class ' . get_class($object) . ' with: << ' . $sql . ' >> using ' . implode(', ', $data));
		$statement = $this->dbHandle->prepare($sql);
		$statement->execute($data);
		$dbModel->setId($object, $this->dbHandle->lastInsertId());
	}

	private function updateObject($object)
	{
		$dbModel = DbModel::getDbModel(get_class($object));
		$dbModel->doCallback(DbModel::BEFORE_UPDATE, $object);
		$dbModel->doCallback(DbModel::BEFORE_SAVE, $object);
		$update = $dbModel->getUpdate($object);
		if (is_null($update)) {
			//オブジェクトが変わってない
			return;
		}
		$sql = $update['sql'];
		$data = $update['data'];
		Logger::info('DatabaseSession:query -- update class ' . get_class($object) . ' with: << ' . $sql . ' >> using ' . implode(', ', $data));
		$statement = $this->dbHandle->prepare($sql);
		$statement->execute($data);
		return $statement->rowCount();
	}

	private function deleteObject($object)
	{
		$dbModel = DbModel::getDbModel(get_class($object));
		$dbModel->doCallback(DbModel::BEFORE_DELETE, $object);
		$delete = $dbModel->getDelete($object);
		$sql = $delete['sql'];
		$data = $delete['data'];
		Logger::info('DatabaseSession:query -- delete class ' . get_class($object) . ' with: << ' . $sql . ' >> using ' . implode(', ', $data));
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