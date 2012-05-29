<?php

class FileRenderer extends BaseRenderer
{
	public function renderHttpResponse($data, $httpRequest, $httpResponse)
	{
		$filePath = $data['path'];
		$fileName = $data['name'];
		if (!file_exists($filePath)) {
			throw new Exception('FileRenderer::customRender() -- ファイルは見つかりません。');
		}
		$httpResponse->skipAll();

		header('Content-length ' . filesize($filePath));
		header('Content-type: ' . mime_content_type($filePath));
		//	IEなどは文字化けする
		//header('Content-Disposition: attachment; filename=' . $fileName);
		header('Content-Disposition: attachment');
		readfile($filePath);
	}
}