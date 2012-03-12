<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

/*	
 *	formSafe()
 *		XSRFの対策
 */
function smarty_function_formSafe($params, Smarty_Internal_Template $template)
{
	$session = $template->getTemplateVars(SmartyRenderer::SESSION_VAR_NAME);
	$formSafeKey = Config::get(Config::FORM_SAFE_KEY);
	$formSafeValue = $session->get(Session::NONCE_KEY);

	return '<input type="hidden" name="' . htmlspecialchars($formSafeKey) . '" value="' . htmlspecialchars($formSafeValue) . '" />';
}
