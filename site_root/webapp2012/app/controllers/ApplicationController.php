<?php

/*
 *	ApplicationController
 */
class ApplicationController extends BaseController
{
	/*
	 *	失敗（エラー）メッセージのフラグ
	 */
	const MSG_ERROR = 'error';

	/*
	 *	成功メッセージのフラグ
	 */
	const MSG_ALERT = 'alert';

	/*
	 *	（内部よう）　成功・エラーの区分
	 */
	const MSG_TYPE = 'ApplicationController.message_type';

	/*
	 *	（内部用）　成功・エラーメッセージ内容
	 */
	const MSG_CONTENT = 'ApplicationController.message_content';

	public $pageTitle;
	public $pageSubTitle;
	public $pageSection;
	public $pageAlert;
	public $pageError;

	public function initialize()
	{
		$this->addFilter(BaseController::$BEFORE_FILTER, 'preProcess');
		$this->pageTitle = null;
		$this->pageSubTitle = null;
		$this->pageSection = null;
		$this->pageAlert = null;
		$this->pageError = null;
	}

	public function preProcess($params)
	{
		/*
		//セッションにユーザIDがあるか確認。無かったらログインへリダイレクト。
		$session = $this->getSession();
		$userId = $session->getUser();
		if (is_null($userId)) {
			$session->set(AuthController::PRE_LOGIN_URL, $this->getRequest()->getOriginalUri());
			return $this->doRedirect('login_form', null);
		}
		//ユーザIDがあれば、DBレコードが有効のことを確認。無効であればログアウトさせてエラーメッセージを表示する。
		$login = LoginModel::loadId($userId, true);
		if (is_null($login)) {
			return $this->doRedirectWithMessage('login_form', array(), self::MSG_ERROR, 'ユーザは見つかりません。ログインしてください。');
		}
		$this->login = $login;
		Logger::setFile(phgm::$LOG_DIR . DS . 'system.log.' . $this->login->val('user_name'));
		*/

		//	doRedirectWithMessage()からリダイレクトされた場合、メッセージを取得して表示されるようにする。
		$messageType = $this->getPassedData(self::MSG_TYPE);
		$message = $this->getPassedData(self::MSG_CONTENT);
		if (!is_null($messageType) && !is_null($message)) {
			if ($messageType === self::MSG_ERROR) {
				$this->pageError = $message;
			} else if ($messageType === self::MSG_ALERT) {
				$this->pageAlert = $message;
			}
		}
	}

	public function setPageTitle($title)
	{
		$this->pageTitle = $title;
	}

	public function setPageSubTitle($title)
	{
		$this->pageSubTitle = $title;
	}

	public function showError($message)
	{
		$this->pageError = $message;
	}

	public function showAlert($message)
	{
		$this->pageAlert = $message;
	}
	
	/*
	 *	doRedirectWithMessage($route, $params, $messageType, $message)
	 *		指定のルートへリダイレクトして、リダイレクト後に指定の成功・エラーメッセージを表示する。
	 *
	 *	@param $route String ルート名
	 *	@param $params Array ルートパラム
	 *	@param $messageType String メッセージ区分　（MSG_ALERT・MSG_ERROR）
	 *	@param $message String メッセージ内容
	 */
	public function doRedirectWithMessage($route, $params, $messageType, $message)
	{
		$this->setPassData(self::MSG_TYPE, $messageType);
		$this->setPassData(self::MSG_CONTENT, $message);
		return $this->doRedirect($route, $params);
	}
}