<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */
/*
 *	class BaseModel
 *		モデルを代表するクラス。主にデータベースのレコードかフォームのデータになります。
 */

class BaseModel
{
	public function BaseModel()
	{
		
	}

	/*
	 *	set($key[, $value])
	 */
	public function set($key, $value = null)
	{
		if (is_null($key)) {
			throw new Exception('BaseModel:set() -- キーはナルです。');
		}
		if (is_array($key)) {
			foreach ($key as $property => $value) {
				$this->$property = $value;
			}
		} else {
			$this->$key = $value;
		}
	}

	/*
	 *	get()
	 */
	public function get($key)
	{
		
	}

	/*
	 *	fromJSON($json)
	 *		JSONフォーマットのストリングからデータを読み取って設定する
	 *
	 *	params:
	 *		String $json: JSONフォーマットされたストリング。キーはクラスのプロパティーに一致すると設定される
	 *
	 *	returns:
	 *		String: 今オブジェクトのデータをJSONフォーマットにしたストリング
	 */
	public function toJSON()
	{
		
	}

	/*
	 *	fromJSON($json)
	 *		JSONフォーマットのストリングからデータを読み取って設定する
	 *
	 *	params:
	 *		String $json: JSONフォーマットされたストリング。キーはクラスのプロパティーに一致すると設定される
	 *
	 *	returns:
	 *		null
	 */
	public function fromJSON($json)
	{
		
	}

	/*
	 *	isValid()
	 *		正しい状況であるか確認する
	 *
	 *	returns: 
	 *		boolean: true　OKの場合, false 問題がある場合
	 */
	public function isValid()
	{
		return false;
	}

	/*
	 *	create($array)
	 *		（便利メソッド）クラスのオブジェクトを作成して、データを渡したアレーに設定して、返す。
	 *
	 *	params:
	 *		Array $array: （key => value）のようなアレー
	 *
	 *	returns:
	 *		object: クラスのオブジェクト
	 */
	public static function create($array)
	{
		$classname = __CLASS__;
		$instance = new $classname;
		$instance->set($array);
		return $instance;
	}
}