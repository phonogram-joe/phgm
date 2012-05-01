<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class FileUpload {

	private static $ERROR_CODES;
	private static $UPLOAD_DIRECTORY;

	private $originalName = null;
	private $temporaryUploadPath = null;
	private $uploadValidationCode = null;
	private $mimeType = null;
	private $size = 0; // in bytes
	private $errorCode = null;
	private $isUploaded = false;
	private $finalUploadedPath = null;

	public function setOriginalName($name)
	{
		$this->originalName = $name;
	}
	
	public function getOriginalName()
	{
		return $this->originalName;
	}
	
	public function setTemporaryUploadPath($temporaryPath)
	{
		$this->temporaryUploadPath = $temporaryPath;
	}
	
	public function getTemporaryUploadPath()
	{
		return $this->temporaryUploadPath;
	}

	public function setMimeType($type)
	{
		$this->mimeType = $type;
	}
	
	public function getMimeType()
	{
		return $this->mimeType;
	}
	
	public function setSize($size)
	{
		if (is_numeric($size)) {
			$this->size = $size;
		} else {
			throw new Exception('FileUpload::setSize() -- 無効なサイズです。');
		}
	}
	
	public function getSize()
	{
		return $this->size;
	}
	
	public function getExtension()
	{
		return pathinfo($this->getOriginalName(), PATHINFO_EXTENSION);
	}

	public function setErrorCode($code)
	{
		if (false !== array_search($code, self::$ERROR_CODES)) {
			$this->errorCode = $code;
		} else {
			throw new Exception("FileUpload::setErrorCode() -- 無効なエラーコード：　$code");
		}
	}
	
	public function getErrorCode()
	{
		return $this->errorCode;
	}
	
	public function getErrorMessage()
	{
		
		$msg = '不明なエラー';
		switch ($this->errorCode) {
			case UPLOAD_ERR_OK:
				$msg = 'ファイルはアップロードされました。';
				break;
			case UPLOAD_ERR_INI_SIZE:
				$msg = 'ファイルのサイズはphp.iniによりの最大サイズを超えているため、アップロードはできません。';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$msg = 'ファイルのサイズはHTMLフォームのMAX_FILE_SIZEを超えてるため、アップロードできません。';
				break;
			case UPLOAD_ERR_PARTIAL:
				$msg = 'ファイルは不十分にアップロードされました。';
				break;
			case UPLOAD_ERR_NO_FILE:
				$msg = 'ファイルはありませんでした。';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$msg = '一時的なフォルダーは設定されてないためアップロードできませんでした。';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$msg = '書き込み権限がないためアップロードできません。';
				break;
			case UPLOAD_ERR_EXTENSION:
				$msg = 'ファイル拡張子が無効なため、アップロードできません。';
				break;
		}
		
		return $msg;
	}
	
	private function setUploadFailed()
	{
		$this->finalUploadedPath = null;
		$this->isUploaded = false;
	}

	private function setUploadedToPath($uploadPath)
	{
		$this->finalUploadedPath = $uploadPath;
		$this->isUploaded = true;
		$this->setSize(filesize($this->finalUploadedPath));
	}

	public function isUploaded()
	{
		return $this->isUploaded === true;
	}

	public function hasUpload()
	{
		return !is_null($this->originalName);
	}

	private function makeTempName()
	{
		return Session::makeNonce($this->originalName) . '.' . $this->getExtension();
	}

	public function setUploadValidationCode($uploadCode)
	{
		$this->uploadValidationCode = $uploadCode;
	}

	public function getUploadValidationCode()
	{
		return $this->makeTempName();
	}

	public function getUploadPath()
	{
		return $this->finalUploadedPath;
	}

	public function uploadFile()
	{
		if ($this->isUploaded) {
			return true;
		}
		if (!is_null($this->uploadValidationCode)) {
			$realUploadPath = self::$UPLOAD_DIRECTORY . DS . $this->makeTempName();
			$tempUploadPath = self::$UPLOAD_DIRECTORY . DS . $this->uploadValidationCode;
			if ($realUploadPath === $tempUploadPath && file_exists($realUploadPath)) {
				$this->setUploadedToPath($realUploadPath);
				return true;
			} else {
				$this->setUploadFailed();
				return false;
			}
		} else if (!is_null($this->temporaryUploadPath) && $this->errorCode === UPLOAD_ERR_OK) {
			$uploadPath = self::$UPLOAD_DIRECTORY . DS . $this->makeTempName();
			$isUploaded = move_uploaded_file($this->temporaryUploadPath, $uploadPath);
			if ($isUploaded === true) {
				$this->setUploadedToPath($uploadPath);
				return true;
			} else {
				$this->setUploadFailed();
				return false;
			}
		} else {
			return false;
		}
	}

	//-------------------------------------------------------------

	public static function classInitialize()
	{
		self::$ERROR_CODES = array(
			UPLOAD_ERR_OK, 
			UPLOAD_ERR_INI_SIZE, 
			UPLOAD_ERR_FORM_SIZE, 
			UPLOAD_ERR_PARTIAL, 
			UPLOAD_ERR_NO_FILE, 
			UPLOAD_ERR_NO_TMP_DIR, 
			UPLOAD_ERR_CANT_WRITE, 
			UPLOAD_ERR_EXTENSION
		);
		self::$UPLOAD_DIRECTORY = phgm::$UPLOAD_FILES_DIR;
	}

	public static function setUploadDirectory($uploadDirectory)
	{
		if (file_exists($uploadDirectory) && is_dir($uploadDirectory) && is_writable($uploadDirectory)) {
			self::$UPLOAD_DIRECTORY = $uploadDirectory;
		} else {
			throw new Exception('FileUpload::setUploadDirectory() -- [' . $uploadDirectory . '] アップロード先は書き込み権限が与えられた存在するフォルダーであるのを確認してください。');
		}
	}

	public static function isUploadArray($uploadParams)
	{
		return array_key_exists('name', $uploadParams) && array_key_exists('error', $uploadParams) && array_key_exists('tmp_name', $uploadParams);
	}

	public static function newFromUploadArray($uploadParams)
	{
		if (mb_strlen($uploadParams['name']) <= 0) {
			return null;
		}
		$file = new FileUpload();
		$file->setOriginalName($uploadParams['name']);
		$file->setTemporaryUploadPath($uploadParams['tmp_name']);
		$file->setMimeType($uploadParams['type']);
		$file->setSize($uploadParams['size']);
		$file->setErrorCode($uploadParams['error']);
		$file->uploadFile();
		return $file;
	}

	public static function isTempArray($uploadParams)
	{
		return array_key_exists('name', $uploadParams) && array_key_exists('upload_code', $uploadParams);
	}

	public static function newFromTempArray($uploadParams)
	{
		if (mb_strlen($uploadParams['name']) <= 0) {
			return null;
		}
		$file = new FileUpload();
		$file->setOriginalName($uploadParams['name']);
		$file->setUploadValidationCode($uploadParams['upload_code']);
		$file->uploadFile();
		return $file;
	}
}