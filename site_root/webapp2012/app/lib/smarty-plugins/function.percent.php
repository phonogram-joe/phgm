<?php

function smarty_function_percent($params, Smarty_Internal_Template $template)
{
	$numerator = $params['num'];
	$denominator = $params['by'];
	$percent = 100 * ($numerator / $denominator);
	if ($percent < 0) {
		$percent = 0;
	}

	return sprintf("%01.2f", $percent);
}