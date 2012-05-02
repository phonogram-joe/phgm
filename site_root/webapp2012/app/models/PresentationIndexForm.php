<?php

ClassLoader::load('PresentationModel');
ClassLoader::load('BaseIndexForm');

class PresentationIndexForm extends BaseIndexForm
{
	public static $MODEL_DEFINITION;
	public static $SEARCH_MODEL_CLASS;

	public static function classInitialize()
	{
		$modelDefinition = BaseModel::initializeSubclass(__CLASS__);
		$modelDefinition->defineField('keyword', 'キーワード', 'string', array(), array('null_blank'), true);
		$modelDefinition->defineField('create_at', '登録日', 'date', array(), array('null_blank'), true);
		BaseIndexForm::initializeFormFields(get_class(), $modelDefinition, 'PresentationModel');
	}

	public static function create()
	{
		$form = new PresentationIndexForm();
		$form->val('order', 'create_at:asc');
		return $form;
	}

	public function updateSqlStatement($sqlStatement)
	{
		parent::updateSqlStatement($sqlStatement);
		$keyword = $this->val('keyword');
		if (!is_null($keyword)) {
			$sqlStatement->where(
				'presentation.title like :keyword OR presentation.summary like :keyword',
				array('keyword' => '%' . $keyword . '%')
			);
		}
		$createAt = $this->val('create_at');
		if (!is_null($createAt)) {
			$sqlStatement->where(
				'presentation.create_at >= :start and presentation.create_at < :end',
				array(
					'start' => DatetimeType::toDb(TimeUtils::startOfDay($createAt)),
					'end' => DatetimeType::toDb(TimeUtils::startOfNextDay($createAt))
			));
		}
	}
}