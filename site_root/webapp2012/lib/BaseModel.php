<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */
/*
 *	class BaseModel
 *		モデルを代表するクラス。主にデータベースのレコードかフォームのデータになります。
 */

class BaseModel
{
	private static $CLASS_MODEL_DEFINITIONS;

	public $changedFields;
	public $validationErrors;

	public function BaseModel($values = null)
	{
		$this->changedFields = null;
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		$modelDefinition->initializeObject($this);
		if (!is_null($values)) {
			$modelDefinition->set($this, $values);
		}
		$this->changedFields = array();
		$this->validationErrors = array();
	}

	public static function classInitialize()
	{
		if (is_null(self::$CLASS_MODEL_DEFINITIONS)) {
			self::$CLASS_MODEL_DEFINITIONS = array();
		}
	}

	public static function initializeSubclass($class)
	{
		$reflector = new ReflectionClass($class);
		if (false !== array_search('MODEL_DEFINITION', $reflector->getStaticProperties())) {
			return;
		}
		$modelDefinition = new ModelDefinition($class);
		$reflector->setStaticPropertyValue('MODEL_DEFINITION', $modelDefinition);
		self::$CLASS_MODEL_DEFINITIONS[$class] = $modelDefinition;
		return $modelDefinition;
	}

	public static function getClassModelDefinition($class)
	{
		return self::$CLASS_MODEL_DEFINITIONS[$class];
	}
	
	/*
	 *	set($key[, $value])
	 */
	public function set($key, $value = null)
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->set($this, $key, $value);
	}

	/*
	 *	get()
	 */
	public function get($key)
	{
		return $this->{$key};
	}

	public function getLabel($key)
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->getLabel($key);
	}

	public function getChanged()
	{
		return $this->changedFields;
	}

	public function getValidationErrors()
	{
		return $this->validationErrors;
	}

	public function isValid()
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->isValid($this);
	}
}