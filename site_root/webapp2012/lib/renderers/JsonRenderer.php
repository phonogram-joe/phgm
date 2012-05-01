<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class JsonRenderer extends BaseRenderer
{
	
	public function customRender($data, $httpRequest, $httpResponse)
	{
		if (is_null($data)) {
			return '';
		} else if (is_array($data) && count($data) > 0 && $data[0] instanceof BaseModel) {
			$json = array();
			foreach ($data as $model) {
				$json[] = $model->getAll();
			}
			return json_encode($json);
		} else if ($data instanceof ModelRenderFormat) {
			$json = array();
			$models = $data->getModels();
			$fields = $data->getFieldsList();
			foreach ($models as $model) {
				$item = array();
				foreach ($fields as $key) {
					$item[$key] = $model->{$key};
				}
				$json[] = $item;
			}
			return json_encode($json);
		} else {
			return json_encode($data);
		}
	}

	public function customHttpResponse($data, $httpRequest, $httpResponse)
	{

	}

	public function initialize()
	{

	}
}