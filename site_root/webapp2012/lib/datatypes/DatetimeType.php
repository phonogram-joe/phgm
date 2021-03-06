<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DatetimeType extends BaseDataType
{
	const FORMAT_STR_DATETIME = '%Y-%m-%d %H:%M:%S';
	const FORMAT_STR_DATE = '%Y-%m-%d';
	const DATETIME_REGEX = '/^(\d{4})-(\d{2})-(\d{2})(\s+(\d{2})[:](\d{2})[:](\d{2}))?$/';
	const MATCH_LENGTH_DATETIME = 8;
	const MATCH_LENGTH_DATE = 4;

	const MATCH_INDEX_YEAR = 1;
	const MATCH_INDEX_MONTH = 2;
	const MATCH_INDEX_DAY = 3;
	const MATCH_INDEX_HOUR = 5;
	const MATCH_INDEX_MINUTE = 6;
	const MATCH_INDEX_SECOND = 7;

	public static function fromWeb($value)
	{
		if (!is_null($value) && is_int($value)) {
			return $value;
		}
		$match = array();
		if (0 === preg_match(static::DATETIME_REGEX, $value, $match)) {
			return BaseDataType::$INVALID;
		}
		if (count($match) === static::MATCH_LENGTH_DATETIME) {
			$datetime = mktime(
				$match[static::MATCH_INDEX_HOUR], 
				$match[static::MATCH_INDEX_MINUTE],
				$match[static::MATCH_INDEX_SECOND], 
				$match[static::MATCH_INDEX_MONTH],
				$match[static::MATCH_INDEX_DAY],
				$match[static::MATCH_INDEX_YEAR]
			);
		} else if (count($match) === static::MATCH_LENGTH_DATE) {
			$datetime = mktime(
				0, //hour
				0, //minute
				0, //second
				$match[static::MATCH_INDEX_MONTH],
				$match[static::MATCH_INDEX_DAY],
				$match[static::MATCH_INDEX_YEAR]
			);
		} else {
			return BaseDataType::$INVALID;
		}
		return $datetime;
	}

	public static function toWeb($value)
	{
		if (is_long($value)) {
			return strftime(static::FORMAT_STR_DATETIME, $value);
		} else {
			return $value;
		}
	}

	public static function fromDb($value)
	{
		return static::fromWeb($value);
	}

	public static function toDb($value)
	{
		return static::toWeb($value);
	}
}