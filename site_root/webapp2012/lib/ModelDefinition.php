<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

ClassLoader::load('IntegerType');

class ModelDefinition
{
	private $class;
	private $fields;
	private $validations;
	private $visibilityWhitelist;
	private $visibilityBlacklist;

	public function ModelDefinition($class)
	{
		$this->class = $class;
		$this->fields = array();
		$this->validations = array();
		$this->visibilityWhitelist = array();
		$this->visibilityBlacklist = array();
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getFields()
	{
		return $this->fields;
	}

	/*
	 *	getFieldList([$includeHidden])
	 *		項目名のリストを取得する。デフォルトではプライベート項目を返さないが、$includeHiddenをtrueに指定すると全項目を取得できる。
	 */
	public function getFieldList($includeHidden = false)
	{
		$fields = array();
		foreach ($this->fields as $name => $field) {
			if ($includeHidden || false !== array_search($name, $this->visibilityWhitelist)) {
				$fields[] = $name;
			}
		}
		return $fields;
	}

	/*
	 *	getLabel($key)
	 *		指定の項目名のラベルを取得。$keyはromajiの名で、返すのは（日本語の）ラベル。
	 * 	例： getLabel('name') => '名前'
	 */
	public function getLabel($key)
	{
		if (isset($this->fields[$key])) {
			return $this->fields[$key]['label'];
		} else {
			return $key;
		}
	}

	public function getType($key)
	{
		return $this->fields[$key]['type'];
	}

	public function defineField($name, $label, $type, $validations, $conversions, $isVisible = false)
	{
		$this->fields[$name] = array('name' => $name, 'label' => $label, 'validations' => $validations);
		$type = ClassLoader::toClassName($type, 'Type');
		ClassLoader::load($type);
		$this->fields[$name]['type'] = $type;
		foreach ($validations as $validator) {
			//each validator should be given as a string with method name optionally followed by arguments. separate all segments with a colon eg 'lengthMax:10' or 'lengthRange:8:20'
			$validatorParts = preg_split('/[:]/', $validator);
			$validatorMethod = $validatorParts[0];
			array_shift($validatorParts);
			if (method_exists($this->class, $validatorMethod)) {
				//class method
				$this->validations[] = array($name, null, $validatorMethod, $validatorParts);
			} else {
				//validator class
				$validatorClass = ClassLoader::toClassName($validatorMethod, 'Validator');
				ClassLoader::load($validatorClass);
				$this->validations[] = array($name, $validatorClass, $validatorMethod, $validatorParts);
			}
		}
		$converters = array();
		if (is_null($conversions)) {
			$conversions = array();
		}
		foreach ($conversions as $conversion) {
			$conversionParts = preg_split('/[:]/', $conversion);
			$conversionClass = ClassLoader::toClassName($conversionParts[0], 'Converter');
			ClassLoader::load($conversionClass);
			array_shift($conversionParts);
			$converters[] = array($conversionClass, $conversionParts);
		}
		$this->fields[$name]['converters'] = $converters;
		if ($isVisible === false) {
			$this->visibilityBlacklist[] = $name;
		} else {
			$this->visibilityWhitelist[] = $name;
		}
	}

	public function initializeObject($object)
	{
		foreach ($this->fields as $name => $field) {
			if (!property_exists($object, $name)) {
				//object initialized normally, eg not through PDO which sets properties before constructor called
				$object->{$name} = null;
			} else if (!is_null($object->{$name})) {
				//object initialized through PDO and thus properties already set. will all be strings, convert to internal datatype
				$value = call_user_func(array($this->fields[$name]['type'], 'fromDb'), $object->{$name});
				if ($value === BaseDataType::$INVALID) {
					throw new Exception('ModelDefinition:initializeObject() -- 項目「' . $name . '」：データベースでの価値は無効でした。');
				}
				$object->{$name} = $value;
			}
		}
		$dbModel = DbModel::getDbModel(get_class($object));
		if (!is_null($dbModel)) {
			$idName = $dbModel->getIdName();
			if (property_exists($object, $idName) && !is_null($object->{$idName})) {
				$object->{$idName} = IntegerType::fromDb($object->{$idName});
			}
		}
	}

	public function hasField($key)
	{
		return array_key_exists($key, $this->fields);
	}

	public function getAll($object)
	{
		$values = array();
		foreach ($this->fields as $name => $field) {
			$values[$name] = $this->get($object, $name);
		}
		return $values;
	}

	public function get($object, $key)
	{
		$value = $object->{$key};
		if (is_null($value)) {
			return $value;
		}
		$type = $this->fields[$key]['type'];
		if (!method_exists($type, 'toWeb')) {
			throw new Exception('ModelDefinition:get() -- オブジェクトの ' . $key　. 'というキーは ' . $type .  ' タイプにになっていますが、見つかりません。');
		}
		$convertedValue = call_user_func(array($type, 'toWeb'), $value);
		if ($convertedValue === BaseDataType::$INVALID) {
			return $value;
		}
		return $convertedValue;
	}

	public function getDb($object, $key)
	{
		$value = $object->{$key};
		if (is_null($value)) {
			return $value;
		}
		$type = $this->fields[$key]['type'];
		if (!method_exists($type, 'toWeb')) {
			throw new Exception('ModelDefinition:get() -- オブジェクトの ' . $key　. 'というキーは ' . $type .  ' タイプにになっていますが、見つかりません。');
		}
		$convertedValue = call_user_func(array($type, 'toDb'), $value);
		if ($convertedValue === BaseDataType::$INVALID) {
			return $value;
		}
		return $convertedValue;
	
	}

	public function set($object, $key, $value, $isChanged)
	{
		if (is_null($object) || is_null($key)) {
			throw new Exception('ModelDefinition:set() -- オブジェクトまたはキーはナルです。');
		}
		if (is_array($key)) {
			//Logger::trace('ModelDefinition:set() -- array()');
			$params = $key;
			$setParams = array();
			foreach ($this->fields as $name => $options) {
				if (false !== array_search($name, $this->visibilityWhitelist) && array_key_exists($name, $params)) {
					//Logger::trace('ModelDefinition:set() -- visible, key/value ' . $name . '=' . $params[$name]);
					$setParams[$name] = $this->simpleSet($object, $name, $params[$name], $isChanged);
				}
			}
			return $setParams;
		} else {
			if (!array_key_exists($key, $this->fields)) {
				throw new Exception('ModelDefinition:set() -- ' . $this->class . 'には「' . $key . '」というキーはないです。');
			}
			//Logger::trace('ModelDefinition:set() -- key/value ' . $key . '=' . $value);
			return $this->simpleSet($object, $key, $value, $isChanged);
		}
	}

	private function simpleSet($object, $key, $value, $isChanged)
	{
		foreach ($this->fields[$key]['converters'] as $converter) {
			if (is_null($value)) {
				break;
			}
			$value = call_user_func_array(array($converter[0], 'input'), array_merge((array)$value, $converter[1]));
		}
		if (!is_null($value)) {
			$type = $this->fields[$key]['type'];
			$convertedValue = call_user_func(array($type, 'fromWeb'), $value);
			if ($convertedValue !== BaseDataType::$INVALID) {
				$value = $convertedValue;
			}
		}
		if ($object->{$key} === $value) {
			return $value;
		}
		$object->{$key} = $value;
		if ($isChanged) {
			$object->_change($key, $value);
		}
		return $value;
	}

	public function isValid($object, $validationField)
	{
		$modelValid = true;
		$errorMsg = true;
		$errors = array();

		$fields = is_null($validationField) ? $this->fields : array($validationField => $this->fields[$field]);

		//	validate by data type
		foreach ($fields as $name => $options) {
			if (is_null($object->{$name})) {
				continue;
			}
			$type = $this->fields[$name]['type'];
			$convertedValue = call_user_func(array($type, 'fromWeb'), $object->{$name});
			if ($convertedValue === BaseDataType::$INVALID) {
				$errorMsg = '無効な価値です。';
				$object->addValidationError($name, $errorMsg);
				$errors[$name] = $errorMsg;
			}
		}
		//	validate by validation rules
		foreach ($this->validations as $validation) {
			$name = $validation[0];
			$obj = $validation[1];
			$method = $validation[2];
			$args = $validation[3];

			if (!is_null($validationField) && $name !== $validationField) {
				continue; //only validate the requested fields
			}

			array_unshift($args, $object->{$name});
			if (is_null($obj)) {
				$errorMsg = call_user_func_array(array($object, $method), $args);
			} else {
				$errorMsg = call_user_func_array(array($obj, $method), $args);
			}

			if (!is_null($errorMsg) && !array_key_exists($name, $errors)) {
				$modelValid = false;
				$errors[$name] = $errorMsg;
				$object->addValidationError($name, $errorMsg);
			}
		}
	}
}
