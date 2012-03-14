<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class Session
{
	private $userKey;
	private $flashKey;
	private $nonceKey;
	private $previousFlash;

	const USER_KEY = 'phgm_user';
	const FLASH_KEY = 'phgm_flash';
	const NONCE_KEY = 'phgm_nonce';
	const LAST_ACTIVITY_KEY = 'phgm_last_activity';

	public function Session()
	{
		if ($this->hasKey(self::FLASH_KEY)) {
			$this->previousFlash = (array) $this->clear(self::FLASH_KEY);
		} else {
			$this->previousFlash = array();
		}
		$this->set(self::FLASH_KEY, array());
		$this->set(self::LAST_ACTIVITY_KEY, TimeUtils::now());
	}

	public static function validateSessionConfig()
	{
		$maxNonceCount = Config::get(Config::SESSIONS_MAX_NONCE_COUNT);
		if ($maxNonceCount < 1) {
			throw new Exception('Session::validateSessionConfig() -- SESSIONS_MAX_NONCE_COUNTは1以上であるべきです。');
		}
		$nonceSecret = Config::get(Config::SESSIONS_NONCE_SECRET);
		if (strlen($nonceSecret) < 64 || $nonceSecret === Config::DEFAULT_SESSIONS_NONCE_SECRET) {
			throw new Exception('Session::validateSessionConfig() -- SESSIONS_NONCE_SECRETはデフォルトのと違う64文字以上であるべきです。');
		}
		$sessionName = Config::get(Config::SESSION_NAME);
		if ($sessionName === Config::DEFAULT_SESSION_NAME || strlen($sessionName) < 64 || !preg_match('/[a-zA-Z0-9]+/', $sessionName)) {
			throw new Exception('Session::validateSessionConfig() -- SESSION_NAMEはデフォルトのと違う64文字以上であるべきです。');
		}
	}

	public static function makeSession()
	{
		self::validateSessionConfig();

		$sessionId = session_id();
		session_name(Config::get(Config::SESSION_NAME));
		//	セッション作成
		if (!session_start()) {
			throw new Exception('HttpRequest:setSession() -- セッションの初期化に失敗がありました。');
		}
		//	既存のセッションであれば、タイムアウトの時にセッションを空にして作り直す
		if ($sessionId !== '' && 
				(!isset($_SESSION[self::LAST_ACTIVITY_KEY]) || 
				$_SESSION[self::LAST_ACTIVITY_KEY] - TimeUtils::now() > Config::get(Config::SESSION_TIMEOUT))) {
			Logger::trace('Session::makeSession() -- session timeout. regenerating.');
			$_SESSION = array();
			session_destroy();

			session_name(Config::get(Config::SESSION_NAME));
			if (session_start()) {
				Logger::trace('Session::makeSession() -- regenerating session id');
				session_regenerate_id(true);
			} else {
				throw new Exception('Session::makeSession() -- セッションを作り直してから作成できませんでした。');
			}
		}
		return new Session();
	}

	public function getUser()
	{
		return $this->get(self::USER_KEY);
	}

	public function setUser($user)
	{
		$this->set(self::USER_KEY, $user);
	}

	public function clearUser()
	{
		return $this->clear(self::USER_KEY);
	}

	public function passData($key, $msg)
	{
		$_SESSION[self::FLASH_KEY][$key] = $msg;
	}
	public function clearPassData()
	{
		$this->set(self::FLASH_KEY, array());
	}

	public function getPassedData($key)
	{
		if (isset($this->previousFlash[$key])) {
			return $this->previousFlash[$key];
		}
		return null;
	}

	public function clearPassedData()
	{
		$previousFlash = $this->previousFlash;
		$this->previousFlash = array();
		return $previousFlash;
	}

	public function hasKey($key)
	{
		return isset($_SESSION[$key]);
	}

	/*
	 *	set($key, $value)
	 *		セッションに一般なデータを書き込む。
	 */
	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/*
	 *	get($key)
	 *		セッションからデータを読み込む
	 */
	public function get($key)
	{
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return null;
	}

	/*
	 *	clear($key)
	 *		セッションから指定のキーのデータを消して、消す前の価値を返す。
	 */
	public function clear($key)
	{
		$value = $this->get($key);
		unset($_SESSION[$key]);
		return $value;
	}

	private function makeNonce($data)
	{
		return hash_hmac('sha512', implode('::', array($data, $_SERVER['REMOTE_ADDR'], session_id())), Config::get(Config::SESSIONS_NONCE_SECRET));
	}

	/*
	 *	generateNonce()
	 *		（CSRF対策用） フォームの提出者を確認できるようにランダムなキーを作成してセッションに保存する。フォーム画面を通さないと
	 *		キーの価値を得られないので、CSRFにより投稿されたリクエストは正しい価値に当たらない。
	 *
	 *		フォームに含むには、下記のように{{formFor}}のブロックプラグインを使って下さい。
	 *			{{formFor name='model_delete_form' id=$model->get('id')}}
	 *				<input type="submut" value="削除する" />
	 *			{{/formFor}}
	 */
	public function generateNonce($name)
	{
		//	設定の最大キー数より多ければ、古いキーを削除。
		$nonces = $this->get(self::NONCE_KEY);
		if (!is_null($nonces)) {
			$nonces = explode('::', $nonces);
			while (count($nonces) >= Config::get(Config::SESSIONS_MAX_NONCE_COUNT)) {
				array_shift($nonces);
			}
		} else {
			$nonces = array();
		}
		//	名前にランダムなストリングを加えてキーを作成。
		$name = substr(md5($name . uniqid(mt_rand(), true)), 0, 32);
		$newNonce = $this->makeNonce($name);
		$nonces[] = $newNonce;
		$this->set(self::NONCE_KEY, implode('::', $nonces));
		//	セッションに保存するのはキーのみ、返すのはキーと元の名前
		return $newNonce . '::' . base64_encode($name);
	}

	/*
	 *	isValidNonce($testNonce)
	 *		（CSRF対策用）　フォームに含まれた提出者確認のキーが有効なのか判断する。
	 *		Config::SESSIONS_MAX_NONCE_COUNTはキー何個まで同時保存されるか指定する。
	 *		少なければ、複数のタブを使う場合に間違って断れる可能性はあります。
	 */
	public function isValidNonce($testNonce)
	{
		//	キーは「キー::名前」になってるので、分ける
		$parts = explode('::', $testNonce);
		if (count($parts) !== 2) {
			Logger::trace('Session:isValidNonce() -- nonce is NOT valid: does not follow "nonce::name" pattern.');
			return false;
		}
		$testNonce = $parts[0];
		$name = base64_decode($parts[1]);
		
		//	キーはセッションに保存されてるか確認する
		$nonces = $this->get(self::NONCE_KEY);
		if (is_null($nonces) || false === strpos($nonces, $testNonce)) {
			Logger::trace('Session:isValidNonce() -- nonce is NOT valid: value not in session.');
			return false;
		}

		//	渡されたキーの名前により再びキーを作成する。渡されたキーと同じであることを確認。
		$realNonce = $this->makeNonce($name);
		if ($realNonce !== $testNonce) {
			Logger::trace('Session:isValidNonce() -- nonce is NOT valid: differs from expected value. ' . $realNonce . ' ' . $testNonce);
			return false;
		}

		return true;
	}
}