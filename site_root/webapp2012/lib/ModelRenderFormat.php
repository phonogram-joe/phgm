<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

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