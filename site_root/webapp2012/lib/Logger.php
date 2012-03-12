<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class Logger
{
	//	ログレベルのオプション
	const TRACE = 1;
	const DEBUG = 2;
	const INFO = 3;
	const WARN = 4;
	const ERROR = 5;
	const FATAL = 6;

	private static $INSTANCE;

	private $filepath;
	private $level;

    private function __construct() {
    	$this->filepath = null;
    	$this->level = self::ERROR;
    }
    private function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    private function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
	
	private function logMessage($level, $message)
	{
		$buf = "" . date("Y/m/d H:i:s") . " [" . $level . "] " . $message . "\n";
		$this->write($buf);
	}

	private function logError($level, $message, $filename, $line, $error = null)
	{
		$buf = "" . date("Y/m/d H:i:s") . " [" . $level . "] " . $message;
		$buf .= " (" . $filename . ":" . $line . ")\n";
		if (!is_null($error)) {
			$trace = $error->getTraceAsString();
			foreach (explode("\n", $trace) as $traceline) {
				$buf .= "\t" . $traceline . "\n";
			}
		}
		$this->write($buf);
	}

	private function write($msg)
	{
		if (is_null($this->filepath)) {
			return true;
		}

		$fp = fopen($this->filepath, 'a');
		if ($fp === false) {
			print $msg;
			throw new Exception('ログファイルを開けませんでした。');
		}

		if (fwrite($fp, $msg) === false) {
			@fclose($fp);
			throw new Exception('');
			return false;
		}

		if (fclose($fp) === false) {
			print $msg;
			throw new Exception('ログファイルを正しく閉じられませんでした。');
		}

		return true;
	}

	public static function classInitialize()
	{
		if (is_null(self::$INSTANCE)) {
    		self::$INSTANCE = new Logger();
		}

		return self::$INSTANCE;
	}

	public static function setLevel($level)
	{
		self::$INSTANCE->level = $level;
	}

	public static function setFile($filepath)
	{
		self::$INSTANCE->filepath = $filepath;
	}

	public static function trace($message)
	{
		if (self::$INSTANCE->level <= self::TRACE) {
			self::$INSTANCE->logMessage('TRACE', $message);
		}		
	}
	public static function debug($message)
	{
		if (self::$INSTANCE->level <= self::DEBUG) {
			self::$INSTANCE->logMessage('DEBUG', $message);
		}		
	}
	public static function info($message)
	{
		if (self::$INSTANCE->level <= self::INFO) {
			self::$INSTANCE->logMessage('INFO', $message);
		}		
	}
	public static function warn($message)
	{
		if (self::$INSTANCE->level <= self::WARN) {
			self::$INSTANCE->logMessage('WARN', $message);
		}		
	}
	public static function error($error)
	{
		if (self::$INSTANCE->level <= self::ERROR) {
			self::$INSTANCE->logError('ERROR', $error->getMessage(), $error->getFile(), $error->getLine(), $error);
		}		
	}
	public static function fatal($error)
	{
		if (self::$INSTANCE->level <= self::FATAL) {
			self::$INSTANCE->logError('FATAL', $error->getMessage(), $error->getFile(), $error->getLine(), $error);
		}		
	}
}