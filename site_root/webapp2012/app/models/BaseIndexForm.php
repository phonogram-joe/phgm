<?php

class BaseIndexForm extends BaseModel
{
	private static $NON_CONDITION_FIELDS = null;
	const DEFAULT_ITEM_COUNT = 5;
	const MAX_ITEM_COUNT = 25;
	private $modelClassName;

	public static function classInitialize()
	{
		if (is_null(self::$NON_CONDITION_FIELDS)) {
			self::$NON_CONDITION_FIELDS = array('page_index', 'page_item_count', 'page_total', 'page_item_total', 'order', '_quicksearch');
		}
	}

	public static function initializeFormFields($indexFormClass, $modelDefinition, $modelClass)
	{
		$modelDefinition->defineField('page_index', 'ページ', 'first_array_key_int', array('validatePagingIndex'), array(), true);
		$modelDefinition->defineField('page_item_count', '１ページの項目数', 'integer', array('validatePagingItemCount'), array('null_blank'), true);
		$modelDefinition->defineField('page_total', '結果ページ数', 'integer', array(), array('null_blank'), true);
		$modelDefinition->defineField('page_item_total', '結果数', 'integer', array(), array('null_blank'), true);
		$modelDefinition->defineField('order', '並び順', 'string', array('order_by:' . $modelClass), array('null_blank'), true);
		$modelDefinition->defineField('_quicksearch', '簡単検索', 'string', array(), array('null_blank'), true);

		$reflector = new ReflectionClass($indexFormClass);
		$reflector->setStaticPropertyValue('SEARCH_MODEL_CLASS', $modelClass);
	}

	public function updateSqlStatement($sqlStatement)
	{
		$sqlStatement->paging($this->getPageItemCount(), $this->getPageIndex());
		if (!is_null($this->val('order'))) {
			$reflector = new ReflectionClass(get_class($this));
			$modelClass = $reflector->getStaticPropertyValue('SEARCH_MODEL_CLASS');
			$sqlStatement->orderBy(DbModel::getDbModel($modelClass)->getTableName() . '.' . str_replace(':', ' ', $this->val('order')));
		}
	}

	public function getPageIndex()
	{
		return is_null($this->page_index) ? 0 : $this->page_index;
	}

	public function getPageItemCount()
	{
		return is_null($this->page_item_count) ? self::DEFAULT_ITEM_COUNT : $this->page_item_count;
	}

	public function getPageTotal()
	{
		if (is_null($this->page_total)) {
			throw new Exception('RequestIndexForm::getPageTotal() -- 検索結果数は指定されてないため、取得はできません。');
		}
		return $this->page_total;
	}

	public function hasPageIndex($index)
	{
		return $index >= 0 && $index < $this->getPageTotal();
	}

	public function getResultTotal()
	{
		if (is_null($this->page_item_total)) {
			throw new Exception('RequestIndexForm::getResultTotal() -- 検索結果数は指定されてないため、取得はできません。');
		}
		return $this->page_item_total;
	}

	public function setResultTotal($total)
	{
		$this->val('page_total', ceil($total / $this->getPageItemCount()));
		$this->val('page_item_total', $total);
	}

	public function hasPreviousPage()
	{
		return $this->getPageIndex() > 0;
	}

	public function hasNextPage()
	{
		return $this->getPageIndex() + 1 < $this->getPageTotal();
	}

	public function isConditionField($key)
	{
		return false === array_search($key, self::$NON_CONDITION_FIELDS);
	}

	public function hasConditions()
	{
		foreach ($this->getFieldsList() as $key) {
			if (false === array_search($key, self::$NON_CONDITION_FIELDS) && !is_null($this->{$key})) {
				return true;
			}
		}
		return false;
	}

	public function matches($indexForm)
	{
		foreach ($this->getFieldsList() as $key) {
			if ($this->isConditionField($key)) {
				
			} else if (is_null($this->val($key)) &&
				is_null($indexForm->val($key))) {
				//same
			} else if ($this->val($key) !== $indexForm->val($key)) {
				return false;
			}
		}
		return true;
	}

	public function validatePagingIndex($paging)
	{
		return (is_null($paging) || (is_integer($paging) && $paging >= 0)) ? null : '無効です';
	}

	public function validatePagingItemCount($count)
	{
		return (is_null($count) || (is_integer($count) && $count > 0  && $count < self::MAX_ITEM_COUNT)) ? null : '無効です';
	}

}