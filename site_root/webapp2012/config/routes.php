<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

function defineRoutes($router)
{
	$router->setUrlPrefix('');
	$router->setDefaultController('DefaultController');
	$router->setErrorController('ErrorController');

	//	デフォルト
	$router->map('top', 				'GET /', 					'HomeController', 			'home');

	//	認証
	$router->map('auth:login_form', 	'GET  /auth/login', 		'AuthController', 			'loginForm');
	$router->map('auth:login_save', 	'POST /auth/login', 		'AuthController', 			'loginSave');
	$router->map('auth:logout', 		'GET  /auth/logout', 		'AuthController', 			'logout');

	//	API (AJAX用)
	$router->map('items',			'GET  /api/items',			'ItemController',			'items');
	$router->map('item_create',		'POST /api/item',			'ItemController',			'newItem');
	$router->map('item_update',		'PUT  /api/item/#id',		'ItemController',			'updateItem');
	$router->map('item_delete',		'DELETE  /api/item/#id',	'ItemController',			'deleteItem');

}
