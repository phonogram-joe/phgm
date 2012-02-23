<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BaseViewModel
{
	private $model;

	public function BaseViewModel($model)
	{
		$this->model = $model;
	}

	public static function mapToViews($modelArray)
	{
		if (empty($rsArray)) {
			return array();
		}
		return array_map(array(__CLASS__, 'makeView'), $modelArray);
	}

	public static function create($model)
	{
		$class = __CLASS__;
		return new $class($model);
	}

	public function __call($name, $args)
	{
		return $this->model->get($name);
	}

	public function __get($name)
	{
		return call_user_func(array($this, $name));
	}

	public function toJSON()
	{
		return get_object_vars($this->model);
	}
}
