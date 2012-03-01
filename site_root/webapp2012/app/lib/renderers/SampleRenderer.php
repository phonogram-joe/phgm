<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SampleRenderer
{
	public function initialize()
	{
		$this->format = HttpResponseFormat::$JSON;
	}

	public function customRender($data, $httpResponse)
	{
		return json_encode($data);
	}
	
	public function customHttpResponse($data, $httpResponse)
	{
	}
}