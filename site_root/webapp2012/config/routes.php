<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

function defineRoutes($router)
{
	$router->setUrlPrefix('/webapp2012/');
	$router->setDefaultController('SampleController');
	$router->setErrorController('ErrorController');

	//	プレゼンテーション
	$router->map('admin:presentation_index', 'GET /admin/presentations', 'AdminPresentationController', 'index');
	$router->map('admin:presentation_show', 'GET /admin/presentations/#id', 'AdminPresentationController', 'show');
	$router->map('admin:presentation_new_form', 'GET /admin/presentations/new', 'AdminPresentationController', 'newForm');
	$router->map('admin:presentation_new_save', 'POST /admin/presentations/new', 'AdminPresentationController', 'newSave');
	$router->map('admin:presentation_edit_form', 'GET /admin/presentations/#id/edit', 'AdminPresentationController', 'editForm');
	$router->map('admin:presentation_edit_save', 'POST /admin/presentations/#id/edit', 'AdminPresentationController', 'editSave');

	//	プレゼンテーションのスライド
	$router->map('admin:presentation_slides', 'GET /admin/presentations/#id/slides', 'AdminSlideController', 'slides');
	$router->map('admin:presentation_slide_edit', 'POST /admin/presentations/#id/slides/edit', 'AdminSlideController', 'editSlides');
	$router->map('admin:presentation_slide_new', 'POST /admin/presentations/#id/slides/new', 'AdminSlideController', 'newSlide');




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
