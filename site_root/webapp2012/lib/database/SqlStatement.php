<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SqlStatement
{
	private $sql;
	private $mainClause;
	private $whereClauses;
	private $pagingClause;
	private $orderByClause;
	private $values;
	private $data;

	public function __construct($sqlString, $valueSubstitutions = array())
	{
		$this->values = $valueSubstitutions;
		$this->data = array();
		$this->sql = null;
		$this->whereClauses = array();
		$this->pagingClause = null;
		$this->orderByClause = null;

		$this->mainClause = preg_replace_callback('/:(\w+)/', array($this, '__substitute'), $sqlString);
	}

	public function __substitute($matches)
	{
		$this->data[] = $this->values[$matches[1]];
		return '?';
	}

	public function getSql()
	{
		if (is_null($this->sql)) {
			$sql = $this->mainClause;
			if (count($this->whereClauses) > 0) {
				$sql .= ' WHERE (' . implode(' AND ', $this->whereClauses) . ')';
			}
			if (!is_null($this->orderByClause)) {
				$sql .= $this->orderByClause;
			}
			if (!is_null($this->pagingClause)) {
				$sql .= $this->pagingClause;
			}
			$this->sql = $sql;
		}
		return $this->sql;
	}

	public function getData()
	{
		return $this->data;
	}

	public function where($conditions, $values)
	{
		if (is_array($values)) {
			$where = new SqlStatement($conditions, $values);
			$this->whereClauses[]= $where->getSql();
			$this->data = array_merge($this->data, $where->getData());
		} else {
			$this->whereClauses[] = $conditions;
			$this->data = array_merge($this->data, array($values));
		}
	}

	public function orderBy($orderBy)
	{
		if (!is_null($orderBy)) {
			$this->orderByClause = ' ORDER BY ' . $orderBy;
		}
	}

	public function paging($perPage, $pageIndex)
	{
		if (!is_null($perPage)) {
			$sql = ' LIMIT ' . intval($perPage);
			if (!is_null($pageIndex)) {
				$sql .= ' OFFSET ' . (intval($perPage) * intval($pageIndex));
			}
			$this->pagingClause = $sql;
		}
	}
}