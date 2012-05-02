<?php

require_once(phgm::$VENDOR_DIR . DS . 'php-markdown' . DS . 'markdown.php');

function smarty_modifier_markdown($text) {
	/*
	TODO: XSS prevention
	*/
	$html = Markdown($text);
	//$html = HTML_Purifier($html);
	return $html;
}
