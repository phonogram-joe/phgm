<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BaseDecorator
{
	public function BaseDecorator()
	{
	}

	public static function modelToDecorator($model)
	{
		$klass = __CLASS__;
		$decorator =  new $klass();
		$values = $model->getAll();
		foreach ($values as $key => $value) {
			$decorator->{$key} = $value;
		}
		return $decorator;
	}

	public function get($name)
	{
		if (method_exists($this, $name)) {
			return call_user_func(array($this, $name));
		}
		return $this->{$name};
	}

	public function __get($name)
	{
		return $this->{$name};
	}

	public function toJSON()
	{
		return get_object_vars($this);
	}
}
