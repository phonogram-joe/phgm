<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class FileUploadValidator
{
	public static function file_upload($value)
	{
		if (is_object($value) && $value instanceof FileUpload) {
			return $value->hasUpload() && !$value->isUploaded() ? 'アップロードに失敗しました。' : null;
		} else {
			return null;
		}
	}
}