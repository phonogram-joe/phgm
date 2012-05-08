<?php

function smarty_modifier_htmlify($text) {
	$text = preg_replace_callback(Htmlify::TAG_REGEX, array('Htmlify', 'replaceTags'), $text);
	$text = preg_replace_callback(Htmlify::LINK_REGEX, array('Htmlify', 'replaceLinks'), $text);
	$text = preg_replace_callback(Htmlify::IMAGE_REGEX, array('Htmlify', 'replaceImages'), $text);
	$text = preg_replace_callback(Htmlify::HEADER_REGEX, array('Htmlify', 'replaceHeaders'), $text);
	$text = nl2br($text);
	return $text;
}

class Htmlify {
	const TAG_REGEX = '/([_*])([^_*]+)([_*])/';
	const LINK_REGEX = '/\[([^\]]+)\s([^\]]+?)\]/';
	const IMAGE_REGEX = '/img\(([^\)]+)\)/';
	const HEADER_REGEX = '/(\#+)\s+(.*)/';

	public static function replaceTags($matches)
	{
		switch ($matches[1]) {
			case '_':
				$tag = 'i';
				break;
			case '*':
				$tag = 'b';
				break;
			default:
				$tag = null;
				break;
		}
		$html = htmlspecialchars($matches[2]);
		if (!is_null($tag)) {
			$html = "<$tag>" . $html . "</$tag>";
		}
		return $html;
	}

	public static function replaceLinks($matches)
	{
		$html = htmlspecialchars($matches[1]);
		$href = htmlspecialchars($matches[2], ENT_QUOTES);
		return "<a href=\"$href\">$html</a>";
	}

	public static function replaceImages($matches)
	{
		$src = htmlspecialchars($matches[1], ENT_QUOTES);
		return "<img src=\"$src\" />";
	}

	public static function replaceHeaders($matches)
	{
		$html = htmlspecialchars($matches[2]);
		$count = mb_strlen($matches[1]);
		if ($count < 1 || $count > 6) {
			$count = 1;
		}
		return "<h$count>$html</h$count>";
	}
}