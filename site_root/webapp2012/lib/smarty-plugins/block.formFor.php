<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

/*	
 *	formFor()
 *		XSRF対策のフォーム
 *	{{formFor name=sample_edit_save id=1 html_id='edit_form' html_class='error'}}
 *		...
 *	{{/formFor}}
 *
 *	=>
 *
 *	<form name="sample_edit_save" method="POST" action="/sample/1/edit" id="edit_form" class="error">
 *	<input type="hidden" name="__form_id" value="*****************::*********" />
 *	...
 *	</form>
 */
function smarty_block_formFor($params, $content, Smarty_Internal_Template $template, &$repeat)
{
	if ($repeat) {
		$name = array_key_exists('name', $params) ? $params['name'] : null;
		unset($params['name']);
		$html_id = array_key_exists('html_id', $params) ? $params['html_id'] : null;
		unset($params['html_id']);
		$html_class = array_key_exists('html_class', $params) ? $params['html_class'] : null;
		unset($params['html_class']);
		$http_method = array_key_exists('http_method', $params) ? $params['http_method'] : 'POST';
		unset($params['http_method']);

		$http_method = strtoupper($http_method);
		if (!HttpRequest::isHttpMethod($http_method)) {
			throw new Exception('smarty_block_formFor() -- 「' . $http_method . '」はHTTPメソッドではありません。');
		}

		$url = Router::getRouter()->urlForName($name, $params);
		if (is_null($url)) {
			throw new Exception('smarty_block_formFor() -- 「' . $name . '」ルートは見つかりません。');
		}

		if ($http_method !== HttpRequest::GET) {
			$session = $template->getTemplateVars(SmartyRenderer::SESSION_VAR_NAME);
			$formSafeKey = Config::get(Config::FORM_SAFE_KEY);
			$formSafeValue = $session->generateNonce($name);
		} else {
			$formSafeKey = null;
		}

		$html  = '<form name="' . $name . '" method="' . $http_method . '" action="' . $url . '" id="' . htmlspecialchars($html_id) . '" class="' . htmlspecialchars($html_class) . '">';
		if (!is_null($formSafeKey)) {
			$html .= '<input type="hidden" name="' . htmlspecialchars($formSafeKey) . '" value="' . htmlspecialchars($formSafeValue) . '" />';
		}
	} else {
		$html = $content . '</form>';
	}
	return $html;
}
