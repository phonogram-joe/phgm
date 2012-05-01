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
		$http_method = array_key_exists('http_method', $params) ? $params['http_method'] : null;
		unset($params['http_method']);

		$route = Router::getRouter()->getNamedRoute($name);
		if (is_null($route)) {
			throw new Exception('smarty_block_formFor() -- 「' . $name . '」ルートは見つかりません。');
		}

		$url = $route->attemptCreateUrl($params);
		if (is_null($url)) {
			throw new Exception('smarty_block_formFor() -- 「' . $name . '」ルートにはパラムが無効。 ' . implode(', ', $params));
		}

		$http_method = strtoupper(is_null($http_method) ? $route->getHttpVerb() : $http_method);
		if (!HttpRequest::isHttpMethod($http_method)) {
			throw new Exception('smarty_block_formFor() -- 「' . $http_method . '」はHTTPメソッドではありません。');
		}

		$formSafeKey = null;
		$formMethodKey = null;
		if ($http_method !== HttpRequest::GET) {
			$session = $template->getTemplateVars(SmartyRenderer::SESSION_VAR_NAME);
			$formSafeKey = Config::get(Config::FORM_SAFE_KEY);
			$formSafeValue = $session->generateNonce($name);
			if ($http_method !== HttpRequest::POST) {
				$formMethodKey = Config::get(Config::HTTP_METHOD_PARAM);
				$formMethodValue = $http_method;
				$http_method = HttpRequest::POST;
			}
		}

		$html  = '<form enctype="multipart/form-data" name="' . $name . '" method="' . $http_method . '" action="' . $url . '" id="' . htmlentities($html_id) . '" class="' . htmlentities($html_class) . '">';
		if (!is_null($formSafeKey)) {
			$html .= '<input type="hidden" name="' . htmlentities($formSafeKey) . '" value="' . htmlentities($formSafeValue) . '" />';
		}
		if (!is_null($formMethodKey)) {
			$html .= '<input type="hidden" name="' . htmlentities($formMethodKey) . '" value="' . htmlentities($formMethodValue) . '" />';
		}
	} else {
		$html = $content . '</form>';
	}
	return $html;
}
