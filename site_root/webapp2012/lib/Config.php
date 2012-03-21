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
	}

	public static function readEnvironment()
	{
		if (isset($_ENV['PHGM_ENVIRONMENT'])) {
			$env = $_ENV['PHGM_ENVIRONMENT'];
			switch ($env) {
				case self::ENVIRONMENT_PRODUCTION:
					self::$SETTINGS[self::ENVIRONMENT] = self::ENVIRONMENT_PRODUCTION;
					break;
				case self::ENVIRONMENT_TEST:
					self::$SETTINGS[self::ENVIRONMENT] = self::ENVIRONMENT_TEST;
					break;
				case self::ENVIRONMENT_DEVELOPMENT:
					self::$SETTINGS[self::ENVIRONMENT] = self::ENVIRONMENT_DEVELOPMENT;
					break;
			}
		}
	}

	public static function getAll()
	{
		return self::$SETTINGS;
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