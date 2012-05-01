<?php

class SqlResultSet
{
	private $results;
	private $totalResultCount;
	private $pageIndex;
	private $pageItemCount;

	public function __construct($results, $totalResultCount, $sqlStatement)
	{
		$this->results = $results;
		$this->totalResultCount = $totalResultCount;
		$this->pageIndex = $sqlStatement->getPageIndex();
		$this->pageItemCount = $sqlStatement->getPageItemCount();
	}

	public function getResults()
	{
		return $this->results;
	}

	public function getTotalResultCount()
	{
		return $this->totalResultCount();
	}

	public function getPageIndex()
	{
		return $this->pageIndex;
	}

	public function getPageItemCount()
	{
		return $this->pageItemCount;
	}
}