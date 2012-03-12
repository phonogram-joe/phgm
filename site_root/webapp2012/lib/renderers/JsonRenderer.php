<?php

class JsonRenderer extends BaseRenderer
{
	
	public function customRender($data, $httpResponse)
	{
		return json_encode($data);
	}

	public function customHttpResponse($data, $httpResponse)
	{

	}

	public function initialize()
	{

	}
}