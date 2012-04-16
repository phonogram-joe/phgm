<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

ClassLoader::loadFrom('RouteMatch', phgm::$LIB_DIR);

class Route
{
	//	デフォルトの「:param」のパラム分
	public static $DEFAULT_CONDITION = '([-_a-zA-Z0-9]+)';
	//	数字のみの「#param」のパラム分
	public static $HASH_CONDITION = '([0-9]+)';
	//	文字のみの「@param」のパラム分
	public static $AT_CONDITION = '([-_a-zA-Z]+)';

	private static $PARAM_PATTERN = '@[\:\@\#][\w]+@';
	private static $PARAM_PATTERN_PREFIX = '@[\:\@\#]';
	private static $PARAM_PATTERN_SUFFIX = '@';

	private $name;
	private $http_verb;
	private $url_pattern;
	private $url_param_names;
	private $matcher;
	private $controller;
	private $action;
	private $params;
	private $conditions;

	public function Route($name, $verbUrl, $controller, $action, $params, $conditions)
	{
		$this->name = $name;
		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
		$this->conditions = $conditions;
		$this->url_param_names = array();
		
		$matchParts = preg_split('/\s+/', $verbUrl, 2);	
		if (count($matchParts) === 2) {
			$this->http_verb = strtoupper($matchParts[0]);
			$this->url_pattern = $matchParts[1];
		} else {
			throw new Exception('Route:__construct -- URLパタンは「GET /url」のようにHTTPメソッド・スペース・URLで書いてください。');
		}

		$this->matcher = null;
		$this->createRegex();
	}	

	private function createRegex()
	{
		//	convert the url_pattern into a regex, converting param placeholders into regex segments that match the param conditions
		$regex = preg_replace_callback(self::$PARAM_PATTERN, array($this, 'createRegexCallback'), $this->url_pattern);
		$this->matcher = '@^' . $regex . '/?$@';

		//	store the param names used within the url_pattern, in order. first char of each is /:#@/ type delimiter, so discard it.
		$paramNames = array();
		preg_match_all(self::$PARAM_PATTERN, $this->url_pattern, $paramNames, PREG_PATTERN_ORDER);
		$paramNames = $paramNames[0];
		foreach ($paramNames as $param) {
			$this->url_param_names[] = substr($param, 1);
		}
	}

	private function createRegexCallback($matches)
	{
		$paramType = substr($matches[0], 0, 1);
		$paramName = substr($matches[0], 1);
		//	when a condition is supplied, assume it is valid regex and use it as is
		if (array_key_exists($paramName, $this->conditions)) {
			return '(' . $this->conditions[$paramName] . ')';
		} else {
			//	else use one of a set number of default conditions based on the placeholder prefix
			switch ($paramType) {
				case '#':
					return self::$HASH_CONDITION;
					break;
				case '@':
					return self::$AT_CONDITION;
					break;
				case ':':
				default:
					return self::$DEFAULT_CONDITION;
					break;
			}
		}
	}

	public function getRegex()
	{
		return $this->matcher;
	}

	public function isConflict($route)
	{
		return $this->matcher === $route->matcher && (is_null($this->http_verb) || is_null($route->http_verb) || $this->http_verb === $route->http_verb);
	}

	public function getName()
	{
		return $this->name;
	}
	public function getController()
	{
		return $this->controller;
	}
	public function getAction()
	{
		return $this->action;
	}
	public function getHttpVerb()
	{
		return $this->http_verb;
	}
	public function getUrlPattern()
	{
		return $this->url_pattern;
	}

	/*
	 *	ERROR/TODO: url from route details fails
	 *	attemptCreateUrl($controller, $action, $params)
	 *		コントローラ名・アクション名・パラムをこのルートのパタンに基づいてURLに変換する。コントローラやアクションが合わない場合または
	 *		パラムの内容が条件に一致しないばあいはナルを返す。すべて一致する場合はURLストリングを返す。
	 */
	public function attemptCreateUrl($params = array())
	{
		$queryParams = array();
		$urlPath = $this->url_pattern;
		foreach ($params as $param => $value) {
			if (false === array_search($param, $this->url_param_names)) {
				//	if the param name is not present in the url_pattern, it must be added as a query parameter to the final URL
				$queryParams[$param] = $value;
			} else {
				//	param name is in url pattern, swap value in
				//	attempt to swap in the parameter for a placeholder in the url_pattern
				$urlPath = preg_replace(self::$PARAM_PATTERN_PREFIX . $param . self::$PARAM_PATTERN_SUFFIX, urlencode($value), $urlPath);
				if (is_null($urlPath)) {
					return null;
				}
			}
		}
		//	check the completed URL and see if it matches this route's criteria
		if (is_null($this->attemptMatchRoute($urlPath, null))) {
			return null;
		}
		return $urlPath . (count($queryParams) === 0 ? '' : ('?' . http_build_query($queryParams)));
	}

	public function attemptMatchRoute($url, $httpVerb)
	{
		$paramValues = array();
		if (!is_null($httpVerb) && !is_null($this->http_verb) && $this->http_verb !== $httpVerb) {
			return null;
		}
		if (preg_match($this->matcher, $url, $paramValues)) {
			array_shift($paramValues);
			if (count($this->url_param_names) > 0) {
				$params = array_combine($this->url_param_names, $paramValues);
				foreach ($params as $key => $value) {
					$params[$key] = urldecode($value);
				}
				$params = array_merge($this->params, $params);
			} else {
				$params = array();
			}
			$match = new RouteMatch($this->controller, $this->action, $params);
			return $match;
		}
		return null;
	}
}
