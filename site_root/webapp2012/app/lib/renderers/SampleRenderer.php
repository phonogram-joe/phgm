<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SampleRenderer
{
	public function initialize()
	{
		
	}

	public function customRender($data)
	{
		return json_encode($data);
	}
}