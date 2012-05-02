<?php

class OrderByValidator
{
	public static function order_by($order, $className)
	{
		if (is_null($order)) {
			return null;
		}
		$fields = BaseModel::getClassModelDefinition($className)->getFieldList(true);

		//	should be 'column:asc' or 'column:desc'
		$parts = explode(':', $order);
		if (count($parts) !== 2) {
			return '無効です。';
		}
		$column = $parts[0];
		$ascDesc = $parts[1];
		if ($ascDesc !== 'asc' && $ascDesc !== 'desc') {
			return '無効です。';
		}
		//	column must be a defined field
		if (false !== array_search($column, $fields)) {
			return null;
		}
		return '無効な並び順。';
	}
}