<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class Config
{
	const ENVIRONMENT = 'config.environment';
	const ENVIRONMENT_DEVELOPMENT = 'dev';
	const ENVIRONMENT_TEST = 'test';
	const ENVIRONMENT_PRODUCTION = 'prod';

	const DATABASE_ENABLED = 'database.enabled';

	const SESSIONS_ENABLED = 'session.enabled';
	const SESSION_NAME = 'session.name';
	const SESSIONS_MAX_NONCE_COUNT = 'session.max_nonce_count';
	const SESSIONS_NONCE_SECRET = 'session.nonce_secret';
	const SESSION_TIMEOUT = 'session.timeout';

	const FATAL_ERROR_MESSAGE = 'config.fatal_error_message';

	const SMARTY_LEFT_DELIMITER = 'smarty.left_delimiter';
	const SMARTY_RIGHT_DELIMITER = 'smarty.right_delimiter';

	const HTTP_METHOD_PARAM = 'routing.http_method_param';

	/* フォームの提出者を確認するように、すべてのフォームに非表示項目にランダムなキーを埋め込む。セッションでもその価値を持って、一致するか登録際に確認。その非表示の項目を設定する。 */
	const FORM_SAFE_KEY = 'security.form_safe_key';

	const DEFAULT_HTTP_METHOD_PARAM = '__http_method';
	const DEFAULT_FORM_SAFE_KEY = '__form_id';
	const DEFAULT_FATAL_ERROR_MESSAGE = 'エラーが発生しました。';
	const DEFAULT_SESSIONS_ENABLED = false;
	const DEFAULT_SESSION_NAME = 'phgm_session_id';
	const DEFAULT_SESSIONS_MAX_NONCE_COUNT = 0;
	const DEFAULT_SESSIONS_NONCE_SECRET = '';
	const DEFAULT_SESSION_TIMEOUT = 72000; //20分
	const DEFAULT_DATABASE_ENABLED = false;

	private static $SETTINGS = null;
	private static $IS_INITIALIZED = false;

	public static function classInitialize()
	{
		if (self::$IS_INITIALIZED) {
			return;
		}
		self::$IS_INITIALIZED = true;
		self::$SETTINGS = array();
		self::setDefaults();
	}

	private static function setDefaults()
	{
		self::$SETTINGS[self::HTTP_METHOD_PARAM] = self::DEFAULT_HTTP_METHOD_PARAM;
		self::$SETTINGS[self::FORM_SAFE_KEY] = self::DEFAULT_FORM_SAFE_KEY;
		self::$SETTINGS[self::FATAL_ERROR_MESSAGE] = self::DEFAULT_FATAL_ERROR_MESSAGE;
		self::$SETTINGS[self::SESSIONS_ENABLED] = self::DEFAULT_SESSIONS_ENABLED;
		self::$SETTINGS[self::SESSION_NAME] = self::DEFAULT_SESSION_NAME;
		self::$SETTINGS[self::SESSIONS_MAX_NONCE_COUNT] = self::DEFAULT_SESSIONS_MAX_NONCE_COUNT;
		self::$SETTINGS[self::SESSIONS_NONCE_SECRET] = self::DEFAULT_SESSIONS_NONCE_SECRET;
		self::$SETTINGS[self::SESSION_TIMEOUT] = self::DEFAULT_SESSION_TIMEOUT;
		self::$SETTINGS[self::DATABASE_ENABLED] = self::DEFAULT_DATABASE_ENABLED;
		self::$SETTINGS[self::ENVIRONMENT] = null;
	}

	public static function isDevTest()
	{
		$env = self::$SETTINGS[self::ENVIRONMENT];
		return $env === self::ENVIRONMENT_DEVELOPMENT || $env === self::ENVIRONMENT_TEST;
	}

	public static function readEnvironment()
	{
		if (isset($_ENV['PHGM_ENVIRONMENT'])) {
			$env = $_ENV['PHGM_ENVIRONMENT'];
		} else {
			$env = apache_getenv('PHGM_ENVIRONMENT');
			if (false === $env) {
				$env = null;
			}
		}
		if (!is_null($env) && is_string($env)) {
			if (false === preg_match('/^[a-zA-Z][-_a-zA-Z0-9]*$/', $env)) {
				throw new Exception('Config::readEnvironment() -- 環境設定は無効です。');
			}
			self::$SETTINGS[self::ENVIRONMENT] = $env;
			return $env;
		} else {
			throw new Exception('Config::readEnvironment() -- 環境は設定してありません。');
		}
	}

	public static function getAll()
	{
		$settings = array();
		foreach (self::$SETTINGS as $key => $value) {
			if ($key !== self::SESSIONS_NONCE_SECRET && $key !== self::SESSION_NAME) {
				$settings[$key] = $value;
			}
		}
		return $settings;
	}

	public static function toString()
	{
		$str = '';
		foreach (self::$SETTINGS as $key => $value) {
			$str .= $key . ' = ' . $value . "\n";
		}
		return $str;
	}

	public static function get($key)
	{
		if (isset(self::$SETTINGS[$key])) {
			return self::$SETTINGS[$key];
		}
		return null;
	}

	public static function set($key, $value)
	{
		self::$SETTINGS[$key] = $value;
	}
}