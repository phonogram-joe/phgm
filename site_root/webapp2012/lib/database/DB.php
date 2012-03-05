<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class DB
{
	private static $IS_INITIALIZED = false;
	private static $CONNECTION_DEFINITIONS;
	private static $CONNECTIONS;	
	private static $DEFAULT_DATABASE;

	public static function classInitialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$IS_INITIALIZED = true;
		self::$CONNECTIONS = array();
		self::$CONNECTION_DEFINITIONS = array();
		self::$DEFAULT_DATABASE = null;
	}

	public static function addConnection($adapter, $host, $dbname, $username, $password, $isDefault = null)
	{
		self::$CONNECTIONS[$dbname] = null;
		self::$CONNECTION_DEFINITIONS[$dbname] = array($adapter . ':host=' . $host . ';dbname=' . $dbname, $username, $password);
		if ($isDefault || is_null(self::$DEFAULT_DATABASE)) {
			self::$DEFAULT_DATABASE = $dbname;
		}
	}

	public static function getSession($dbname = null)
	{
		$dbname = is_null($dbname) ? self::$DEFAULT_DATABASE : $dbname;
		$dbh = null;
		if (!array_key_exists($dbname, self::$CONNECTIONS)) {
			throw new Exception('DB::getSession() -- 「」というデータベースは設定されてないです。');
		} else if (!is_null(self::$CONNECTIONS[$dbname])) {
			$dbh = self::$CONNECTIONS[$dbname];
		} else {
			$definition = self::$CONNECTION_DEFINITIONS[$dbname];
			$dbh = new PDO($definition[0], $definition[1], $definition[2]);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$CONNECTIONS[$dbname] = $dbh;
		}
		return new DatabaseSession($dbh);
	}
}