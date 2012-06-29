<?php

function smarty_function_timeTag($params, Smarty_Internal_Template $template)
{
	$time = array_key_exists('time', $params) ? $params['time'] : TimeUtils::now();
	$format = array_key_exists('format', $params) ? $params['format'] : '%Y年%m月%d日 %H:%M';
	$isTwitterStyle = array_key_exists('twitter', $params);

	//	を日本語にしたい
	if (strpos($format, '%w') !== false) {
		$day = (int) strftime('%w', $time);
		$days = array('日', '月', '火', '水', '木', '金', '土', '日');
		$day = $days[$day];
		$format = preg_replace('/\%w/', $day, $format);
	}

	if (is_null($time)) {
		return '';
	}


	if ($isTwitterStyle) {
		$now = TimeUtils::now();
		$delta = abs($now - $time);
		$suffix = $time < $now ? '前' : '後';
		$date = '';
		if ($delta > 604800) {
			$date =  strftime($format, $time);
		} else if ($delta <= 172800 && $delta > 86400) {
			if ($time < $now) {
				$date = '昨日';
			}
			else {
				$date = '明日';
			}
		} else if ($delta > 86400) {
			$date =  floor($delta / 86400) . '日' . $suffix;
		} else if ($delta > 3600) {
			$date =  floor($delta / 3600) . '時間' . $suffix;
		} else if ($delta > 60) {
			$date =  floor($delta / 60) . '分' . $suffix;
		} else {
			$date =  '只今';
		}
	} else {
		$date = strftime($format, $time);
	}
	if (false === $date || -1 === $date) {
		$date = htmlspecialchars($time);
	} else {
		$date = htmlspecialchars($date);
	}

	//	tooltipで日時を表示する
	$fulldate = strftime($format, $time);
	if (false === $fulldate || -1 === $fulldate) {
		$fulldate = '';
	} else if ($isTwitterStyle) {
		$fulldate = ' rel="tooltip" data-original-title="' . htmlspecialchars($fulldate) . '"';
	} else {
		$fulldate = '';
	}

	return '<time ' . $fulldate . ' datetime="' . DatetimeType::toWeb($time) . '">' . $date . '</time>';
}