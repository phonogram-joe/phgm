<?php

ClassLoader::load('DatetimeType');

class AccessDateType extends DatetimeType
{
	//	ウェブ・DBのデータ形式に変換する場合は親クラスのデフォルト
	//const FORMAT_STR_DATETIME = '%Y-%m-%d %H:%M:%S';
	//const FORMAT_STR_DATE = '%Y-%m-%d';

	const DATETIME_REGEX = '/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})(\s+(\d{1,2})[:](\d{1,2})[:](\d{1,2}))?$/';
	const MATCH_LENGTH_DATETIME = 8;
	const MATCH_LENGTH_DATE = 4;

	const MATCH_INDEX_YEAR = 1;
	const MATCH_INDEX_MONTH = 2;
	const MATCH_INDEX_DAY = 3;
	const MATCH_INDEX_HOUR = 5;
	const MATCH_INDEX_MINUTE = 6;
	const MATCH_INDEX_SECOND = 7;
}