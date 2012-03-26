<?php

class CsvRenderer extends BaseRenderer
{
	public function customRender($data, $httpRequest, $httpResponse)
	{
		if (! ((is_array($data) && count($data) > 0) || $data instanceof ModelRenderFormat)) {
			return '';
		}

		$csv = tmpfile();

		if ($data instanceof ModelRenderFormat || $data[0] instanceof BaseModel) {
			//	モデルのアレーの場合は項目のヘッダー行と、同じ順番でモデルのデータ

			//	RendererData describes an array of models and the keys to render
			if ($data instanceof ModelRenderFormat) {
				$fields = $data->getFieldsList();
				$data = $data->getModels();
			} else {
				//	otherwise just use all the fields
				$fields = $data[0]->getFieldsList();
			}

			$header = array();
			foreach ($fields as $key) {
				$header[] = $data[0]->getLabel($key);
			}
			fputcsv($csv, $header);

			foreach ($data as $model) {
				$row = array();
				foreach ($fields as $key) {
					$row[] = $model->get($key);
				}
				fputcsv($csv, $row);
			}
		} else if (is_object($data[0])) {
			//	オブジェクトのアレーの場合は、変数名をヘッダーとして使って、同じ順番でオブジェクトのデータ
			$keys = array_keys(get_object_vars($data[0]));
			fputcsv($csv, $keys);

			foreach ($data as $object) {
				$row = array();
				foreach ($keys as $key) {
					if (isset($object->{$key})) {
						$row[] = $object->{$key};
					} else {
						$row[] = null;
					}
				}
				fputcsv($csv, $row);
			}
		} else if (is_array($data[0])) {
			//	アレーのアレーの場合はそれぞれのアレーを直接CSVに変換
			foreach ($data as $arrayItem) {
				fputcsv($csv, $arrayItem);
			}
		} else {
			throw new Exception('ModelCsvRenderer:customRender() -- データアレーのアイテムはモデル・オブジェクト・アレーであるべきです。');
		}

		fseek($csv, 0);
		$csv_data = '';
		while (!feof($csv)) {
			$csv_data .= fread($csv, 1024);
		}
		fclose($csv);

		return $csv_data;
	}

	public function customHttpResponse($data, $httpRequest, $httpResponse)
	{

	}

	public function initialize()
	{

	}
}