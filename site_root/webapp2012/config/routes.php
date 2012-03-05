<?php

function defineRoutes($router)
{
	$router->setUrlPrefix('/webapp2012/');
	$router->setDefaultController('SampleController');
	$router->setErrorController('ErrorController');


	$router->mapRest('sample', '/sample', 'SampleController');
	/*	上記の一行は、下記の８ルートとなります：

	$router->map('sample_index', 		'GET /sample', 				'SampleController', 'index');
	$router->map('sample_new_form',		'GET /sample/new', 			'SampleController', 'newForm');
	$router->map('sample_new_save',		'POST /sample/new', 		'SampleController', 'newSave');
	$router->map('sample_show',			'GET /sample/#id', 			'SampleController', 'show');
	$router->map('sample_edit_form',	'GET /sample/#id/edit', 	'SampleController', 'editForm');
	$router->map('sample_edit_save',	'POST /sample/#id/edit', 	'SampleController', 'editSave');
	$router->map('sample_delete_form',	'GET /sample/#id/delete', 	'SampleController', 'deleteForm');
	$router->map('sample_delete_save',	'POST /sample/#id/delete', 	'SampleController', 'deleteSave');
	*/
}
