<?php

ClassLoader::load('PresentationSlideModel');

class PresentationModel extends BaseModel
{
	public static $MODEL_DEFINITION;

	public static function classInitialize()
	{
		$modelDefinition = BaseModel::initializeSubclass(__CLASS__);
		if (is_null($modelDefinition)) {
			return;
		}
		$modelDefinition->defineField('title', 'タイトル', 'string', array('required', 'string_length:10:32'), array('null_blank'), true);
		$modelDefinition->defineField('summary', '内容説明', 'multi_line_string', array('required'), array('null_blank'), true);
		$modelDefinition->defineField('create_at', '作成日時', 'datetime', array(), null, false);
		$modelDefinition->defineField('update_at', '更新日時', 'datetime', array(), null, false);
		$modelDefinition->defineField('delete_at', '削除日時', 'datetime', array(), null, false);

		$dbModel = DbModel::initializeSubclass(__CLASS__);
		$dbModel->addCallback(DbModel::BEFORE_INSERT, 'onDbInsert');
		$dbModel->addCallback(DbModel::BEFORE_UPDATE, 'onDbUpdate');
	}

	public static function create()
	{
		return new PresentationModel();
	}

	public static function loadAll($searchForm)
	{
		$db = DB::getSession();
		$table = DbModel::getDbModel('PresentationModel');
		$sqlStatement = $table->getSelectSql();	
		$searchForm->updateSqlStatement($sqlStatement);
		$results = $db->findAllSqlStatement($sqlStatement, 'PresentationModel');
		$searchForm->setResultTotal($db->lastFindAllRowCount());
		return $results;
	}

	public static function loadAllWithFirstSlide($searchForm)
	{
		$db = DB::getSession();
		$sql = <<<SQL
		SELECT 
			presentation.id,
			presentation.title,
			presentation.summary,
			ps.content as first_slide_content
		FROM presentation AS presentation
		LEFT JOIN presentation_slide ps
		ON presentation.id = ps.presentation_id
		LEFT OUTER JOIN presentation_slide AS ps2
		ON presentation.id = ps2.presentation_id
			AND ps2.order_by < ps.order_by
SQL;
		$sqlStatement = new SqlStatement($sql);
		$searchForm->updateSqlStatement($sqlStatement);
		$sqlStatement->where('ps2.presentation_id IS NULL', array());
		$results = $db->findAllSqlStatement($sqlStatement, 'PresentationModel');
		$searchForm->setResultTotal($db->lastFindAllRowCount());
		return $results;
	}

	public static function loadId($id)
	{
		$db = DB::getSession();
		$presentation = $db->findOne('PresentationModel', $id);
		$presentation->slides = $db->findAll('PresentationSlideModel', 'presentation_id = :id', array('id' => $id), null, null, 'order_by asc');
		return $presentation;
	}	

	public function onDbInsert()
	{
		$this->val('create_at', TimeUtils::now());
	}

	public function onDbUpdate()
	{
		$this->val('update_at', TimeUtils::now());
	}
}