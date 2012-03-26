<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SqlStatement
{
	private $sql;
	private $values;
	private $data;

	public function SqlStatement($sqlString, $valueSubstitutions = array())
	{
		$this->sql = $sqlString;
		$this->values = $valueSubstitutions;
		$this->data = array();

		$this->sql = preg_replace_callback('/:(\w+)/', array($this, 'substitute'), $this->sql);
	}

	public function substitute($matches)
	{
		$this->data[] = $this->values[$matches[1]];
		return '?';
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function getData()
	{
		return $this->data;
	}

	public function where($conditions, $values)
	{
		$where = new SqlStatement($conditions, $values);
		$this->sql .= ' WHERE ' . $where->getSql();
		$this->data = array_merge($this->data, $where->getData());
	}

	public function orderBy($orderBy)
	{
		if (!is_null($orderBy)) {
			$this->sql .= ' ORDER BY ' . $orderBy;
		}
	}

	public function paging($perPage, $pageIndex)
	{
		if (!is_null($perPage)) {
			$this->sql .= ' LIMIT ' . intval($perPage);
			if (!is_null($pageIndex)) {
				$this->sql .= ' OFFSET ' . (intval($perPage) * intval($pageIndex));
			}
		}
	}
}