<?php

class ModelRenderFormat
{
	private $models;
	private $fieldsList;

	public function __construct($models, $fieldsList)
	{
		$this->models = $models;
		$this->fieldsList = $fieldsList;
	}

	public function getModels()
	{
		return $this->models;
	}

	public function getFieldsList()
	{
		return $this->fieldsList;
	}
}