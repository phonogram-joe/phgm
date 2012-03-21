<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class JoinStatement
{
	private $sql;
	private $baseClass;
	private $baseClassIdName;
	private $baseClassIdColumn;
	private $columnsToProperties;

	public function JoinStatement($baseClass, $joinParams)
	{
		$this->baseClass = $baseClass;
		$baseDbModel = DbModel::getDbModel($baseClass);
		$baseClassPrefix = ClassLoader::classNamePrefix($baseClass);

		/*
		$db->join(
			'RequestModel',
			array(
				'submit_login' => 'submit_login_id:LoginModel:id',
				'admin_company' => 'admin_company_id:AdminCompanyModel:id',
				'comments' => 'id:RequestCommentModel:request_id'
			),
			'request.id = :id AND submit_login.id = :login_id',
			array('id' => 1, 'login_id' => 0)
		)

		SELECT request.id, request.subject, submit_login.id, submit_login.name, admin_company.id, admin_company.name, comments.id, comments.comment
		FROM request AS request,
		LEFT JOIN login AS submit_login ON request.submit_login_id = submit_login.id
		LEFT JOIN admin_company AS admin_company ON request.admin_company_id = admin_company.id
		LEFT JOIN request_comment AS comments ON request.id = comments.request_id

		*/

		$columnsToProperties = array();
		$joins = array();

		//	base model fields
		$this->baseClassIdName = $baseDbModel->getIdName();
		$this->baseClassIdColumn = $baseClassPrefix . '__' . $this->baseClassIdName;
		$columnsToProperties[$this->baseClassIdColumn] = array(null, $this->baseClassIdName, null);
		foreach (BaseModel::getClassModelDefinition($baseClass)->getFields() as $field => $fieldProps) {
			$columnsToProperties[$baseClassPrefix . '__' . $field] = array(null, $field, null);
		}
		foreach ($joinParams as $joinName => $joinDetail) {
			$joinDetail = explode(':', $joinDetail);
			if (count($joinDetail) !== 3) {
				throw new Exception('JoinStatement() -- ジョインはベースコラム・モデル名・ジョインコラムの全てが必要です。');
			}
			//base class column for ON, class to join to, and that join table's column for ON
			$baseColumn = $joinDetail[0];
			$joinClass = $joinDetail[1];
			$joinColumn = $joinDetail[2];
			$joinDbModel = DbModel::getDbModel($joinClass);

			ClassLoader::load($joinClass);

			//	join fields
			$columnsToProperties[$joinName . '__' . $joinDbModel->getIdName()] = array($joinName, $joinDbModel->getIdName(), $joinClass);
			foreach (BaseModel::getClassModelDefinition($joinClass)->getFields() as $field => $fieldProps) {
				$columnsToProperties[$joinName . '__' . $field] = array($joinName, $field, null);
			}
			$joins[] = ' LEFT JOIN ' . $joinDbModel->getTableName() . ' AS ' . $joinName . ' ON ' . $baseClassPrefix . '.' . $baseColumn . ' = ' . $joinName . '.' . $joinColumn;
		}
		$columns = array();
		foreach ($columnsToProperties as $name => $properties) {
			$columns[] = str_replace('__', '.', $name) . ' AS ' . $name;
		}
		$sql = 'SELECT ' . implode(',', $columns);
		$sql .= ' FROM ' . $baseDbModel->getTableName() . ' AS ' . $baseClassPrefix;
		$sql .= implode(' ', $joins);

		$this->sql = $sql;
		$this->columnsToProperties = $columnsToProperties;
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function processResults($results)
	{
		$baseClass = $this->baseClass;
		$baseClassIdName = $this->baseClassIdName;
		$baseClassIdColumn = $this->baseClassIdColumn;
		$columnsToProperties = $this->columnsToProperties;
		$resultObjects = array();
		$currentObject = null;
		$previousObject = null;
		foreach ($results as $result) {
			if (!is_null($previousObject) && $previousObject->get($baseClassIdName) === $result[$baseClassIdColumn]) {
				throw new Exception('JoinStatement:processResults() -- １：Nとジョインはできません。別のクエリでデータを取得してください。');
			} else {
				$currentObject = new $baseClass();
			}
			foreach ($columnsToProperties as $column => $properties) {
				$key = $properties[0];
				$field = $properties[1];
				$klass = $properties[2];
				if (is_null($key)) {
					//base class property
					$currentObject->{$field} = $result[$column];
				} else if (!is_null($klass)) {
					//join object ID
					if (is_null($result[$column])) {
						$currentObject->{$key} = null;
					} else {
						$joinObject = new $klass();
						$joinObject->{$field} = $result[$column];
						$currentObject->{$key} = $joinObject;
					}
				} else if (!is_null($currentObject->{$key})) {
					//join object property
					$currentObject->{$key}->{$field} = $result[$column];
				}
			}
			$resultObjects[] = $currentObject;
			$previousObject = $currentObject;
		}
		return $resultObjects;
	}
}