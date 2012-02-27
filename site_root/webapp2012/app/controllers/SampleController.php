<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SampleController extends BaseController
{
	public $message = 'Hello There! 宜しくお願いします！';
	public $fmt;

	public function index($params)
	{
		$this->fmt = $this->getRenderFormat();
		$this->doRender();
	}

	public function show($params)
	{
		$this->doRender();
	}
}