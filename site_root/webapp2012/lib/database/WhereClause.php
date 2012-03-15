<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class WhereClause
{
	private $whereSql;
	private $values;
	private $data;

	public function WhereClause($conditionsString, $valueSubstitutions)
	{
		$this->whereSql = $conditionsString;
		$this->values = $valueSubstitutions;
		$this->data = array();

		$this->whereSql = preg_replace_callback('/:(\w+)/', array($this, 'substitute'), $this->whereSql);
	}

	public function substitute($matches)
	{
		$this->data[] = $this->values[$matches[1]];
		return '?';
	}

	public function getSql()
	{
		return ' WHERE ' . $this->whereSql;
	}

	public function getData()
	{
		return $this->data;
	}
}