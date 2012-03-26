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
	private static $IS_INITIALIZED = false;
	private static $CLASS_MODEL_DEFINITIONS;

	private $changedFields;
	private $validationErrors;

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
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$CLASS_MODEL_DEFINITIONS = array();
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

	public function getAll()
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->getAll($this);
	}

	/*
	 *	set($key, $value)
	 *		ウェブからのデータを内部データ刑に変換する
	 */
	public function set($key, $value = null)
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->set($this, $key, $value);
	}

	/*
	 *	get()
	 *		内部のデータ刑をウェブようの価値に変換した価値を返す。HTMLに埋め込めるデータ刑なる
	 */
	public function get($key)
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->get($this, $key);
	}

	/*
	 *	val($key[, $value])
	 *		内部データ刑で(変換せずに）価値を取得・設定する
	 */
	public function val($key, $value = null)
	{
		if ($value === null) {
			return $this->{$key};
		} else {
			$this->{$key} = $value;
			$this->_change($key, $value);
		}
	}

	public function nullVal($key)
	{
		if (!is_null($this->{$key})) {
			$this->{$key} = null;
			$this->_change($key, null);
		}
	}

	public function getFieldsList()
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return array_keys($modelDefinition->getFields());
	}

	public function getLabel($key)
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		return $modelDefinition->getLabel($key);
	}

	public function isChanged($field)
	{
		return array_key_exists($field, $this->changedFields);
	}
	public function hasChanges()
	{
		return count($this->changedFields) !== 0;
	}

	//record original value. if changed more than once, do not record the subsequent values
	public function _change($key, $value)
	{
		if (!array_key_exists($key, $this->changedFields)) {
			$this->changedFields[$key] = $this->{$key};
		}
	}

	public function resetChanges()
	{
		foreach ($this->changedFields as $key => $value) {
			$this->{$key} = $value;
		}
		$this->changedFields = array();
		$this->validationErrors = array();
	}

	public function getValidationErrors()
	{
		return $this->validationErrors;
	}

	public function addValidationError($field, $message)
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		if (!$modelDefinition->hasField($field)) {
			throw new Exception('BaseModel:addValidationError() -- ' . get_class($this) . 'クラスには' . $field . 'というキーはありません。');
		}
		$this->validationErrors[$field] = $message;
	}

	public function setValidationErrors($errors)
	{
		$this->validationErrors = $errors;
	}

	public function isValid()
	{
		$modelDefinition = self::getClassModelDefinition(get_class($this));
		$modelDefinition->isValid($this);
		return !$this->hasErrors();
	}

	public function hasErrors()
	{
		return count($this->validationErrors) > 0;
	}
	public function hasError($key)
	{
		return array_key_exists($key, $this->validationErrors);
	}
	public function getError($key)
	{
		return $this->validationErrors[$key];
	}
}