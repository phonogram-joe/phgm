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

	public function defineField($name, $type, $validations, $kanaConversion = null, $isVisible = false)
	{
		$this->fields[$name] = array('name' => $name, 'type' => $type, 'validations' => $validations, 'kanaConversion' => $kanaConversion);
		foreach ($validations as $validator) {
			//each validator should be given as a string with method name optionally followed by arguments. separate all segments with a colon eg 'lengthMax:10' or 'lengthRange:8:20'
			$validatorParts = preg_split('/[:]/', $validator);
			$validatorMethod = $validatorParts[0];
			array_shift($validatorParts);
			if (method_exists($this->class, $validator)) {
				//class method
				$this->validations[] = array($name, null, $validatorMethod, $validatorParts);
			} else {
				//validator class
				$validatorClass = StringUtils::underscoresToCamel($validatorMethod) . 'Validator';
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
			$object->{$name} = null;
		}
	}

	public function set($object, $key, $value = null)
	{
		if (is_null($object)) {
			throw new Exception('ModelDefinition:set() -- オブジェクトはナルです。');
		}
		if (is_null($key)) {
			throw new Exception('ModelDefinition:set() -- キーはナルです。');
		}
		if (!array_key_exists($key, $this->fields)) {
			throw new Exception('ModelDefinition:set() -- ' . $this->class . 'には「' . $key . '」というキーはないです。');
		}
		if (is_array($key)) {
			$params = $key;
			foreach ($this->fields as $name => $options) {
				if (true === array_search($name, $this->visibilityWhitelist) && array_key_exists($name, $params)) {
					$this->simpleSet($object, $name, $params[$name]);
				} else {
					$this->simpleSet($object, $name, null);
				}
			}
		} else {
			$this->simpleSet($object, $key, $value);
		}
	}

	private function simpleSet($object, $key, $value)
	{
		if (!is_null($this->fields[$key]['kanaConversion'])) {
			$value = mb_convert_kana($value, $this->fields[$key]['kanaConversion']);
		}
		$object->{$key} = $value;
		if (!is_null($object->changedFields)) {
			$object->changedFields[$key] = $value;
		}	
	}

	public function isValid($object)
	{
		$object->validationErrors = array();
		$modelValid = true;
		$errorMsg = true;
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

			if (!is_null($errorMsg)) {
				$modelValid = false;
				$object->validationErrors[] = array($name, $errorMsg);
			}
		}
		return $modelValid;
	}
}
