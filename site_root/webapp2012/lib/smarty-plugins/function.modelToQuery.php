<?php

function smarty_function_modelToQuery($params, Smarty_Internal_Template $template)
{
	$prefix = array_key_exists('prefix', $params) ? $params['prefix'] : null;
	$model = $params['model'];

	if (is_null($prefix)) {
		$prefix = '';
		$suffix = '';
	} else {
		$prefix .= '[';
		$suffix = ']';
	}
	$query = array();
	$fields = $model->getAll();
	foreach ($fields as $key => $value) {
		$query[$prefix . $key . $suffix] = $value;
	}
	return http_build_query($query);
}