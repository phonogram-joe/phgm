<?php

ClassLoader::load('PresentationModel');

class PresentationSlideModel extends BaseModel
{
	public static $MODEL_DEFINITION;

	public static function classInitialize()
	{
		$modelDefinition = BaseModel::initializeSubclass(__CLASS__);
		if (is_null($modelDefinition)) {
			return;
		}
		$modelDefinition->defineField('presentation_id', 'プレゼン', 'foreign_key', array('required', 'validatePresentationId'), array('null_blank'), true);
		$modelDefinition->defineField('order_by', '順番', 'integer', array('required'), array('null_blank'), true);
		$modelDefinition->defineField('content', 'スライド内容', 'multi_line_string', array('required'), array('null_blank'), true);
		$modelDefinition->defineField('create_at', '作成日時', 'datetime', array(), null, false);
		$modelDefinition->defineField('update_at', '更新日時', 'datetime', array(), null, false);
		$modelDefinition->defineField('delete_at', '削除日時', 'datetime', array(), null, false);

		$dbModel = DbModel::initializeSubclass(__CLASS__);
		$dbModel->addCallback(DbModel::BEFORE_INSERT, 'onDbInsert');
		$dbModel->addCallback(DbModel::BEFORE_UPDATE, 'onDbUpdate');
	}

	public static function createFor($presentationId)
	{
		$slide = new PresentationSlideModel();
		$slide->val('presentation_id', $presentationId);
		return $slide;
	}

	public function validatePresentationId($id)
	{
		$count = DB::getSession()->findCount(
			'PresentationModel', 
			'id = :id', 
			array('id' => $id)
		);
		return $count === 1 ? null : 'プレゼンが見つかりません。';
	}

	/*
	public function validateOrderBy($orderBy)
	{
		$sql = 'presentation_id = :presentation_id AND order_by = :order_by';
		$data = array(
			'presentation_id' => $this->val('presentation_id'),
			'order_by' => $orderBy
		);
		if (!is_null($this->id)) {
			$sql .= ' AND id != :id';
			$data['id'] = $this->id;
		}
		$count = DB::getSession()->findCount(
			'PresentationSlideModel', 
			$sql, 
			$data
		);
		return $count === 0 ? null : '順番はユニークではありません。';
	}
	*/

	public function onDbInsert()
	{
		$this->val('create_at', TimeUtils::now());
	}

	public function onDbUpdate()
	{
		$this->val('update_at', TimeUtils::now());
	}
}