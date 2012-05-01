<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class FileRenderer extends BaseRenderer
{
	public function customRender($data, $httpRequest, $httpResponse)
	{
		$filePath = $data['path'];
		$fileName = $data['name'];
		if (!file_exists($filePath)) {
			throw new Exception('FileRenderer::customRender() -- ファイルは見つかりません。');
		}
		$httpResponse->skipAll();

		header('Content-length ' . filesize($filePath));
		header('Content-type: ' . mime_content_type($filePath));
		header('Content-Disposition: attachment; filename=' . $fileName);
		readfile($filePath);
	}
}