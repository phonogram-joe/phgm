<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class Logger
{
	private static $INSTANCE;

	private $filepath;

    private function __construct() {
    	$this->filepath = LOGGER_FILE;
    }
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
	
	public static function getLogger() {
		if (is_null(self::$INSTANCE)) {
    		self::$INSTANCE = new Logger();
		}

		return self::$INSTANCE;
	}

	public function logMessage($level, $message)
	{
		$buf = "" . date("Y/m/d H:i:s") . " [" . $level . "] " . $message . "\n";
		$this->write($buf);
	}

	public function logError($level, $message, $filename, $line, $error = null)
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

	public function write($msg)
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

	public static function trace($message)
	{
		if (LOGGER_LEVEL <= LOG_TRACE) {
			self::getLogger()->logMessage('TRACE', $message);
		}		
	}
	public static function debug($message)
	{
		if (LOGGER_LEVEL <= LOG_DEBUG) {
			self::getLogger()->logMessage('DEBUG', $message);
		}		
	}
	public static function info($message)
	{
		if (LOGGER_LEVEL <= LOG_INFO) {
			self::getLogger()->logMessage('INFO', $message);
		}		
	}
	public static function warn($message)
	{
		if (LOGGER_LEVEL <= LOG_WARN) {
			self::getLogger()->logMessage('WARN', $message);
		}		
	}
	public static function error($error)
	{
		if (LOGGER_LEVEL <= LOG_ERROR) {
			self::getLogger()->logError('ERROR', $error->getMessage(), $error->getFile(), $error->getLine(), $error);
		}		
	}
	public static function fatal($error)
	{
		if (LOGGER_LEVEL <= LOG_FATAL) {
			self::getLogger()->logError('FATAL', $error->getMessage(), $error->getFile(), $error->getLine(), $error);
		}		
	}
}