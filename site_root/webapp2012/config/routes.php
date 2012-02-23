<?php

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
