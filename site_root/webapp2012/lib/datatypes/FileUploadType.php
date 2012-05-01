<?php

class FileUploadType extends BaseDataType
{
	public static function fromWeb($value)
	{
		if (is_object($value) && $value instanceof FileUpload) {
			return $value;
		}
		if (is_array($value) && FileUpload::isUploadArray($value)) {
			return FileUpload::newFromUploadArray($value);
		}
		if (is_array($value) && FileUpload::isTempArray($value)) {
			return FileUpload::newFromTempArray($value);
		}
		return BaseDataType::$INVALID;
	}

	public static function toWeb($value)
	{
		throw new Exception('FileUploadType::toWeb() -- ウェブ上のデータ形はありません。隠し項目などでデータを渡してください。FileUploadクラスを参考に。');
	}

	public static function fromDb($value)
	{
		throw new Exception('FileUploadType::toDb/fromDb() -- データベースには保存できません。アップロードを処理してからファイルの保存先をDBに登録してください。');
	}

	public static function toDb($value)
	{
		throw new Exception('FileUploadType::toDb/fromDb() -- データベースには保存できません。アップロードを処理してからファイルの保存先をDBに登録してください。');
	}	
}