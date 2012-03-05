<?php

function defineRoutes($router)
{
	$router->setUrlPrefix('/webapp2012/');
	$router->setDefaultController('SampleController');
	$router->setErrorController('ErrorController');

	$router->map('sample_index', 		'GET /sample', 				'SampleController', 'index');
	$router->map('sample_new_form',		'GET /sample/new', 			'SampleController', 'newForm');
	$router->map('sample_new_save',		'POST /sample/new', 		'SampleController', 'newSave');
	$router->map('sample_show',			'GET /sample/#id', 			'SampleController', 'show');
	$router->map('sample_edit_form',	'GET /sample/#id/edit', 	'SampleController', 'editForm');
	$router->map('sample_edit_save',	'POST /sample/#id/edit', 	'SampleController', 'editSave');
	$router->map('sample_delete_form',	'GET /sample/#id/delete', 	'SampleController', 'deleteForm');
	$router->map('sample_delete_save',	'POST /sample/#id/delete', 	'SampleController', 'deleteSave');

	//	上記と全く同じルートを一気に作る
	//$router->mapRest('sample', '/restSample', 'SampleController');
}

/*
function defineRoutes($router)
{
	$router->setSubDir('/webapp2012/');
	$router->setDefaultController('SampleController');
	$router->setDefaultAction('index');
	$router->set404Controller('ErrorController');

	//SampleController->index() (ディフォルトで)
	$router->map('/sample');

	//SampleController->show() (設定で)
	$router->map('/sample/show', 'SampleController', 'show');

	//SampleController->show(), $_GET['id']はURLから読み取る。URLの:id部分は数字ではないとルールに一致しない
	$router->map('/sample/show/:id', 'SampleController', 'show', array(), array('id' => '[0-9]+'));
}
*/