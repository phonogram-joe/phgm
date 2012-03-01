<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

/*	
 *	urlFor()
 *		コントローラ・アクションへのルートURLを返す。
 *	
 *	例：
 *		{{urlFor controller='SampleController' action='edit' [params=array('id' => 5)]}}
 *		{{urlFor name='sample_edit' [params=array('id' => 5)]}}
 *		=> /webapp2012/sample/5/edit
 *
 *	@params:
 *		['controller'] コントローラ名
 *		['action'] アクション名
 *		['name'] ルート名
 *		['params'] （任意）　クエリ・ルートのパラム。
 *	'controller'と'action'の両方または'name'が必須。
 *
 *	@returns: コントローラ・アクションへのURL。
 */
function smarty_function_urlFor($params, Smarty_Internal_Template $template)
{
	$router = Router::gerRouter();
	$params = array_key_exists('params', $params) ? $params['params'] : array();
	if (array_key_exists('name', $params)) {
		return $router->urlForName($params['name'], $params);
	} else {
		return $router->urlForRoute($params['controller'], $params['action'], $params);
	}
}
?>
