<?php

class JsonRenderer extends BaseRenderer
{
	
	public function customRender($data, $httpRequest, $httpResponse)
	{
		return json_encode($data);
	}

	public function customHttpResponse($data, $httpRequest, $httpResponse)
	{

	}

	public function initialize()
	{

	}
}