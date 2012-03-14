<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */
class BaseController
{
	public static $ERROR_CONTROLLER_ACTION_NAME = 'handleError';
	private static $RESULT_REDIRECT = 0;
	private static $RESULT_RENDER = 1;
	private static $RESULT_ERROR = 2;

	private $_phgmDefaultActionName;
	private $_phgmDefaultRenderFormat;
	private $_phgmActionName;
	private $_phgmRenderActionName;
	private $_phgmRedirectUrl;
	private $_phgmErrorMessage;
	private $_phgmRenderFormat;
	private $_phgmRenderData;
	private $_phgmResultState;
	private $_phgmIsReturned;
	private $_phgmHttpRequest;
	private $_phgmHttpResponse;
	private $_phgmSession;

	public static $BEFORE_FILTER = 'before';
	private $_beforeFilters;
	public static $AFTER_FILTER = 'after';
	private $_afterFilters;
	public static $AROUND_FILTER = 'around';
	private $_aroundFilters;
	public static $AROUND_FILTER_BEFORE = 0;
	public static $AROUND_FILTER_AFTER = 1;

	public function BaseController($request, $response, $actionName)
	{
		$this->_phgmHttpRequest = $request;
		$this->_phgmHttpResponse = $response;
		$this->_phgmSession = $request->getSession();
		$this->_phgmActionName = $actionName;
		$this->_phgmRenderActionName = $actionName;
		$this->_phgmRedirectUrl = null;
		$this->_phgmErrorMessage = null;
		$this->_phgmRenderFormat = null;
		$this->_phgmRenderData = null;
		$this->_phgmResultState = null;
		$this->_phgmDefaultActionName = null;
		$this->_phgmDefaultRenderFormat = HttpResponseFormat::getDefaultFormat();
		$this->_phgmIsReturned = false;

		$this->_beforeFilters = array();
		$this->_afterFilters = array();
		$this->_aroundFilters = array();
	}

	private static function asArray($controller)
	{
		$objectVars = get_object_vars($controller);
		$array = array();
		foreach ($objectVars as $key => $value) {
            if (preg_match('/^_.*/i', $key)) {
            	continue;
            }
            $array[$key] = $value;
		}
		return $array;
	}

	//do not override
	public function execute($params)
	{
		$this->initialize();
		//	アクション名や応答データ刑がナルの場合はデフォルトに設定する
		if (is_null($this->_phgmRenderFormat)) {
			$this->_phgmRenderFormat = $this->_phgmDefaultRenderFormat;
		}
		if (is_null($this->_phgmActionName)) {
			$this->_phgmActionName = $this->_phgmDefaultActionName;
		}

		$this->doFilters(self::$BEFORE_FILTER, null, $params);
		if ($this->_phgmIsReturned) { return; }

		$this->doFilters(self::$AROUND_FILTER, self::$AROUND_FILTER_BEFORE, $params);
		if ($this->_phgmIsReturned) { return; }

		call_user_func(array($this, $this->_phgmActionName), $params);
		
		$this->doFilters(self::$AROUND_FILTER, self::$AROUND_FILTER_AFTER, $params);
		$this->doFilters(self::$AFTER_FILTER, null, $params);
	}

	private function doFilters($type, $beforeAfter = null, $params)
	{
		if ($type === self::$BEFORE_FILTER) {
			foreach ($this->_beforeFilters as $name) {
				//	アラウンドフィルターの前にこーるする場合、レスポンスが決まったら中止する
				if ($this->_phgmIsReturned) { return; }
				call_user_func(array($this, $name), $params);
			}
		} else if ($type === self::$AFTER_FILTER) {
			foreach ($this->_afterFilters as $name) {
				call_user_func(array($this, $name), $params);
			}
		} else if ($type === self::$BEFORE_FILTER) {
			foreach ($this->_aroundFilters as $name) {
				//	アラウンドフィルターの前にこーるする場合、レスポンスが決まったら中止する
				if ($this->_phgmIsReturned && $beforeAfter === self::$AROUND_FILTER_BEFORE) { return; }
				call_user_func(array($this, $name), $params, $beforeAfter);
			}
		}
	}

	//override to put common initialization code for all actions
	public function initialize()
	{
		
	}

	public function addFilter($type, $name)
	{
		if ($type === self::$BEFORE_FILTER) {
			$this->_beforeFilters[] = $name;
		} else if ($type === self::$AROUND_FILTER) {
			$this->_aroundFilters[] = $name;
		} else if ($type === self::$AFTER_FILTER) {
			$this->_afterFilters[] = $name;
		}
	}

	/*
	 *	getRequest()
	 *		HTTPリクエストを返す
	 */
	public function getRequest()
	{
		return $this->_phgmHttpRequest;
	}

	/*
	 *	getResponse()
	 *		HTTPレスポンスを返す。注意：直接使うとフレームワークとぶつかる可能性が高いので、フレームワークを通す方がお勧めです。
	 *		例えばデータにより画像書き出して返す場合、カスタムなレンダラ(/webapp/app/libs/renderers/)
	 *		とそれに合わせてHttpResponseFormatを設定する。レンダラではコントローラのパラムにより画像を作成する。
	 */
	public function getResponse()
	{
		return $this->_phgmHttpResponse;
	}

	public function getSession()
	{
		return $this->_phgmSession;
	}

	/*
	 *	getPassedData($key)
	 *		「フラッシュ」というセッションによりリリダイレクト後のリクエストに渡すデータを読み取る。
	 */
	public function getPassedData($key)
	{
		return $this->_phgmSession->getPassedData($key);
	}

	/*
	 *	setPassData($key, $value)
	 *		「フラッシュ」というセッションによりリリダイレクト後のリクエストに渡すデータを読み取る。
	 */
	public function setPassData($key, $value)
	{
		$this->_phgmSession->passData($key, $value);
	}

	/*
	 *	doIsReturned()　－－　応答が決まったってことを設定する
	 *		二度コールするとエラーを発生する
	 */
	private function doIsReturned()
	{
		if ($this->_phgmIsReturned) {
			throw new Exception('BaseController::doIsReturned -- レンダー・エラー・リダイレクトは既にコールされてます。');
		}
		$this->_phgmIsReturned = true;
	}

	/*--------------------------------------------------------------
	 *	リダイレクトの場合
	 */

	/*
	 *	doRedirectUrl($url)
	 *		指定のURLへリダイレクト。外部URL向き：内部のURL（ルートがあるもの）はdoRedirect()を使ってください。
	 *	@param $url String このURLへリダイレクトする
	 */
	public function doRedirectUrl($url) {
		$this->doIsReturned();
		$this->_phgmRedirectUrl = $url;
	}

	/*
	 *	doRedirect($routeName[$params[, $passData]])
	 *		指定のルートへリダイレクトする。ルートデータやクエリパラムは$paramsで指定できる。セッションによりリダイレクト先のアクションへデータを渡す場合に$passDataも指定できる。
	 *	例：
	 *		POST /sample/1/edit
	 *			モデルを変更して保存する。
	 *			成功メッセージをリダイレクト後で表示したいので、
	 *			doRedirect('sample_show', array('id' => ...), array('message' => '保存できた！'))
	 *		GET /sample/1
	 *			getPassedData('message') => '保存できた！'
	 */
	public function doRedirect($routeName, $params = array(), $passData = array())
	{
		$this->doIsReturned();
		$this->_phgmRedirectUrl = Router::getRouter()->urlForName($routeName, $params);
		if (is_null($this->_phgmRedirectUrl)) {
			throw new Exception('BaseController:doRedirect() -- そのルートはありません。' . $routeName . ' ' . $params);
		}
		foreach ($passData as $key => $value) {
			$this->_phgmSession->passData($key, $value);
		}
	}
	public function isRedirect()
	{
		return !is_null($this->_phgmRedirectUrl);
	}
	public function getRedirectUrl()
	{
		return $this->_phgmRedirectUrl;
	}

	/*--------------------------------------------------------------
	 *	普段の場合：
	 *		テンプレート：	アクション名 (editSave -> edit_save)
	 *		データ刑：		コントローラのデフォルトのデータ刑
	 *		データ刑：		コントローラのインスタンスデータプロパティー
	 */
	public function doRender()
	{

	}

	/*
	 *	doRenderAction($actionName)
	 *	@param $actionName String 現在のアクション名の代わりに、指定するアクションのテンプレートを使う。
	 */
	public function doRenderAction($actionName)
	{
		$this->doIsReturned();
		$this->_phgmRenderActionName = $actionName;
	}

	/*
	 *	doRenderDataAs([$data[, $format[, $actionName]]])
	 *		指定のデータとデータ刑を使ってHTTP応答の内容を決める。
	 *	@param $data Array レンダーするデータ。任意：デフォルトはコントローラのインスタンスパラム
	 *	@param $format String応答のデータ刑。任意：コントローラのデフォルトデータ刑
	 +	@param $actionName String 任意。テンプレートが必要の場合現在と違うアクションを指定できる。デフォルトは現在のアクション
	 */
	public function doRenderDataAs($data, $format = null, $actionName = null)
	{
		$this->doIsReturned();
		if (!is_null($data)) {
			$this->_phgmRenderData = $data;
		}
		if (!is_null($format)) {
			$this->_phgmRenderFormat = $format;
		}
		if (!is_null($actionName)) {
			$this->_phgmRenderActionName = $actionName;
		}
	}
	public function getRenderFormat()
	{
		return $this->_phgmRenderFormat;
	}
	public function getRenderData()
	{
		if (!is_null($this->_phgmRenderData)) {
			return $this->_phgmRenderData;
		} else {
			return BaseController::asArray($this);
		}
	}
	public function getRenderAction()
	{
		return $this->_phgmRenderActionName;
	}

	/*--------------------------------------------------------------
	 *	エラーの場合
	 */
	public function doError($message)
	{
		$this->doIsReturned();
		$this->_phgmErrorMessage = $message;
	}
	public function isError()
	{
		return !is_null($this->_phgmErrorMessage);
	}
	public function getErrorMessage()
	{
		return $this->_phgmErrorMessage;
	}
}