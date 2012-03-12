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
	const SESSION_GLOBAL_KEY = 'session.global_key';
	const SESSION_USER_KEY = 'session.user_key';
	const SESSION_FLASH_KEY = 'session.flash_key';

	const FATAL_ERROR_MESSAGE = 'config.fatal_error_message';

	const SMARTY_LEFT_DELIMITER = 'smarty.left_delimiter';
	const SMARTY_RIGHT_DELIMITER = 'smarty.right_delimiter';

	const HTTP_METHOD_PARAM = 'routing.http_method_param';

	/* フォームの提出者を確認するように、すべてのフォームに非表示項目にランダムなキーを埋め込む。セッションでもその価値を持って、一致するか登録際に確認。その非表示の項目を設定する。 */
	const FORM_SAFE_KEY = 'security.form_safe_key';

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
		self::$SETTINGS[self::HTTP_METHOD_PARAM] = '__http_method';
		self::$SETTINGS[self::FORM_SAFE_KEY] = '__form_id';
		self::$SETTINGS[self::FATAL_ERROR_MESSAGE] = 'エラーが発生しました。';
		self::$SETTINGS[self::SESSIONS_ENABLED] = false;
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