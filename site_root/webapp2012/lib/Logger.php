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

	public function log($level, $message, $filename, $line, $error = null)
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

		//file_put_contents($this->filepath, $msg, FILE_APPEND);
		//return;

		if (($fp = @fopen($this->filepath, "a")) <= false) {
			print $msg;
			trigger_error("Couldn't open log file '" . $this->filepath . "'.", E_USER_NOTICE);
			return false;
		}

		if (!flock($fp, LOCK_EX)) {
			@fclose($fp);
			print $msg;
			trigger_error("Couldn't lock log file. '" . $this->filepath . "'.", E_USER_NOTICE);
			return false;
		}

		if (fwrite($fp, $line) <= false) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			print $msg;
			trigger_error("Couldn't write log file. '" . $this->filepath . "'.", E_USER_NOTICE);
			return false;
		}

		if (!flock($fp, LOCK_UN)) {
			@fclose($fp);
			print $msg;
			trigger_error("Couldn't unlock log file. '" . $this->filepath . "'.", E_USER_NOTICE);
			return false;
		}

		if (!fclose($fp)) {
			print $msg;
			trigger_error("Couldn't close log file. '" . $this->filepath . "'.", E_USER_NOTICE);
			return false;
		}

		return true;
	}

	public static function trace($error)
	{
		if (LOGGER_LEVEL <= LOG_TRACE) {
			self::getLogger()->log('TRACE', $error->getMessage(), $error->getFile(), $error->getLine());
		}		
	}
	public static function debug($error)
	{
		if (LOGGER_LEVEL <= LOG_DEBUG) {
			self::getLogger()->log('DEBUG', $error->getMessage(), $error->getFile(), $error->getLine());
		}		
	}
	public static function info($error)
	{
		if (LOGGER_LEVEL <= LOG_INFO) {
			self::getLogger()->log('INFO', $error->getMessage(), $error->getFile(), $error->getLine());
		}		
	}
	public static function warn($error)
	{
		if (LOGGER_LEVEL <= LOG_WARN) {
			self::getLogger()->log('WARN', $error->getMessage(), $error->getFile(), $error->getLine());
		}		
	}
	public static function error($error)
	{
		if (LOGGER_LEVEL <= LOG_ERROR) {
			self::getLogger()->log('ERROR', $error->getMessage(), $error->getFile(), $error->getLine(), $error);
		}		
	}
	public static function fatal($error)
	{
		if (LOGGER_LEVEL <= LOG_FATAL) {
			self::getLogger()->log('FATAL', $error->getMessage(), $error->getFile(), $error->getLine(), $error);
		}		
	}
}