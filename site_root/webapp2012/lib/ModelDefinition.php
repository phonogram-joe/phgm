<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

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
	public function getFieldList($includeHidden = false)
	{
	}
	public function getLabel($key)
	{
		return $this->fields[$key]['label'];
	}

	public function defineField($name, $label, $type, $validations, $kanaConversion = null, $isVisible = false)
	{
		$this->fields[$name] = array('name' => $name, 'label' => $label, 'type' => $type, 'validations' => $validations, 'kanaConversion' => $kanaConversion);
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
				ClassLoader::load(VALIDATOR, $validatorClass);
				$this->validations[] = array($name, $validatorClass, $validatorMethod, $validatorParts);
			}
		}
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
				$object->{$name} = null;
			}
		}
	}

	public function hasField($key)
	{
		return array_key_exists($key, $this->fields);
	}

	public function set($object, $key, $value = null)
	{
		if (is_null($object)) {
			throw new Exception('ModelDefinition:set() -- オブジェクトはナルです。');
		}
		if (is_null($key)) {
			throw new Exception('ModelDefinition:set() -- キーはナルです。');
		}
		if (is_array($key)) {
			Logger::trace('ModelDefinition:set() -- array()');
			$params = $key;
			$setParams = array();
			foreach ($this->fields as $name => $options) {
				if (false !== array_search($name, $this->visibilityWhitelist) && array_key_exists($name, $params)) {
					Logger::trace('ModelDefinition:set() -- visible, key/value ' . $name . '=' . $value);
					$setParams[$name] = $this->simpleSet($object, $name, $params[$name]);
				} else {
					Logger::trace('ModelDefinition:set() -- invisible, key/value ' . $name . '=null');
					$setParams[$name] = $this->simpleSet($object, $name, null);
				}
			}
			return $setParams;
		} else {
			if (!array_key_exists($key, $this->fields)) {
				throw new Exception('ModelDefinition:set() -- ' . $this->class . 'には「' . $key . '」というキーはないです。');
			}
			Logger::trace('ModelDefinition:set() -- key/value ' . $key . '=' . $value);
			return $this->simpleSet($object, $key, $value);
		}
	}

	private function simpleSet($object, $key, $value)
	{
		if (!is_null($this->fields[$key]['kanaConversion'])) {
			$value = mb_convert_kana($value, $this->fields[$key]['kanaConversion']);
		}
		if ($object->{$key} === $value) {
			return $value;
		}
		$object->{$key} = $value;
		$object->_change($key, $value);
		return $value;
	}

	public function isValid($object)
	{
		$modelValid = true;
		$errorMsg = true;
		$errors = array();
		foreach ($this->validations as $validation) {
			$name = $validation[0];
			$obj = $validation[1];
			$method = $validation[2];
			$args = $validation[3];

			array_unshift($args, $object->{$name});
			if (is_null($obj)) {
				$errorMsg = call_user_func_array(array($object, $method), $args);
			} else {
				$errorMsg = call_user_func_array(array($obj, $method), $args);
			}

			if (!is_null($errorMsg) && !array_key_exists($name, $errors)) {
				$modelValid = false;
				$errors[$name] = $errorMsg;
			}
		}
		$object->setValidationErrors($errors);
		return $modelValid;
	}
}
