<?php

class DatetimeType extends BaseDataType
{
	const DATE_FORMAT = '%Y-%m-%d %H:%M:%S';
	const DATE_REGEX = '/\d{4}-\d{2}-\d{2} \d{2}[:]\d{2}[:]\d{2}/';

	public static function fromWeb($value)
	{
		if (!is_null($value) && is_int($value)) {
			return $value;
		}
		if (!preg_match(self::DATE_REGEX, $value)) {
			return BaseDataType::$INVALID;
		}
		list($date, $time) = explode(' ', $value);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);
		$datetime = mktime($hour, $minute, $second, $month, $day, $year);
		if ($datetime === false || $datetime === -1) {
			return BaseDataType::$INVALID;
		}
		return $datetime;
	}

	public static function toWeb($value)
	{
		return strftime(self::DATE_FORMAT, $value);
	}

	public static function fromDb($value)
	{
		return self::fromWeb($value);
	}

	public static function toDb($value)
	{
		return self::toWeb($value);
	}
}