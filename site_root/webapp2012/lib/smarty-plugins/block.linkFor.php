<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

function smarty_block_linkFor($params, $content, Smarty_Internal_Template $template, &$repeat)
{
	if ($repeat) {
		$name = array_key_exists('name', $params) ? $params['name'] : null;
		unset($params['name']);
		$html_id = array_key_exists('html_id', $params) ? $params['html_id'] : null;
		unset($params['html_id']);
		$html_class = array_key_exists('html_class', $params) ? $params['html_class'] : null;
		unset($params['html_class']);

		$url = Router::getRouter()->urlForName($name, $params);

		$html  = '<a name="' . $name . '" href="' . $url . '" id="' . htmlentities($html_id) . '" class="' . htmlentities($html_class) . '">';
	} else {
		$html = $content . '</a>';
	}
	return $html;
}
