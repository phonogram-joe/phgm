<?php

class PhgmPDO extends PDO
{
	const MYSQL_DRIVER = 'mysql';

	private $_phgmIsMySql = null;
	private $_phgmLastStatement = null;
	private $_phgmLastStatementCountSql = null;

	public function __construct($dsn, $username = null, $password = null, $driver_options = null)
	{
		parent::__construct($dsn, $username, $password, $driver_options);
		$driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
		$this->_phgmIsMySql = (strtolower($driver) === self::MYSQL_DRIVER);
	}

	public function prepare($statement, $driver_options = null)
	{
		if ($this->_phgmIsMySql) {
			$statement = preg_replace('/^\s*SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $statement);
			$this->_phgmLastStatementCountSql = null;
		} else {
			$this->_phgmLastStatementCountSql = preg_replace('/^\s*SELECT.*FROM/U', 'SELECT count(*) FROM', $statement);
		}
		if (is_null($driver_options)) {
			$this->_phgmLastStatement = parent::prepare($statement);
		} else {
			$this->_phgmLastStatement = parent::prepare($statement, $driver_options);
		}
		return $this->_phgmLastStatement;
	}

	public function lastPreparedRowCount()
	{
		if ($this->_phgmIsMySql) {
			$resultStatement = $this->query("SELECT FOUND_ROWS()");
		} else if (!is_null($this->_phgmLastStatementCountSql)) {
			$resultStatement = $this->query($this->_phgmLastStatementCountSql);
		}
		$result = $resultStatement->fetchColumn();
		$resultStatement->closeCursor();
		return intval($result);
	}
}