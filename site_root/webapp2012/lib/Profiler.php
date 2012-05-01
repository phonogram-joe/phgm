<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 *
 *	Profiler.php
 *		リクエスト処理に当たってのメモリー使用・読込ファイル・経過時間を記録するクラス
 */


class Profiler
{
	private $startTime;
	private $endTime;
	private $elapsed;
	private $peakMemory;
	private $dbQueries;
	private $snapshots;
	private $currentSnapshot;
	private $files;
	private $isEnabled;

	private static $PROFILER_INSTANCE = null;

	public static function classInitialize()
	{
	}

	public static function getProfiler()
	{
		if (is_null(self::$PROFILER_INSTANCE)) {
			self::$PROFILER_INSTANCE = new Profiler();
		}
		return self::$PROFILER_INSTANCE;
	}

	private function __construct()
	{
		$this->isEnabled = false;
		$this->startTime = null;
		$this->endTime = null;
		$this->elapsed = null;
		$this->peakMemory = null;
		$this->dbQueries = array();
		$this->snapshots = array();
		$this->currentSnapshot = null;
		$this->files = array();
	}

	public function start($startTime)
	{
		$this->isEnabled = true;
		$this->startTime = $startTime;
	}

	public function stop()
	{
		if (!$this->isEnabled) {
			return;
		}
		if (!is_null($this->currentSnapshot)) {
			$this->stopSnapshot($this->currentSnapshot);
		}
		$this->endTime = microtime(true);
		$this->elapsed = $this->endTime - $this->startTime;
		$files = get_included_files();
		$filesRoot = dirname(ROOT_DIR);
		foreach ($files as $file) {
			$this->files[] = str_replace($filesRoot, '..', $file);
		}
		$this->peakMemory = memory_get_usage();
	}

	public function startSnapshot($name)
	{
		$this->snapshots[$name] = microtime(true);
		$this->currentSnapshot = $name;
	}

	public function stopSnapshot($name)
	{
		$this->snapshots[$name] = microtime(true) - $this->snapshots[$name];
		$this->currentSnapshot = null;
	}

	public function startDbQuery($sql, $data)
	{
		$queryId = uniqid();
		if (!$this->isEnabled) {
			return $queryId;
		}
		$memory = memory_get_usage();
		//$peakMemory = memory_get_peak_usage();
		$start = microtime(true);
		$this->dbQueries[$queryId] = array(
			'sql' => $sql,
			'data' => $data,
			'memory_start' => $memory,
		//	'peak_memory_start' => $peakMemory,
			'start' => $start
		);
		return $queryId;
	}

	public function stopDbQuery($queryId)
	{
		if (!$this->isEnabled) {
			return;
		}
		$memory = memory_get_usage();
		//$peakMemory = memory_get_peak_usage();
		$end = microtime(true);
		$this->dbQueries[$queryId]['end'] = $end;
		$this->dbQueries[$queryId]['memory_end'] = $memory;
		$this->dbQueries[$queryId]['elapsed'] = $end - $this->dbQueries[$queryId]['start'];
		//$this->dbQueries[$queryId]['peak_memory_end'] = $peakMemory;
	}

	public function elapsedTime()
	{
		$elapsed = $this->endTime - $this->startTime;
		return $elapsed;
	}

	public function toJSON()
	{
		if (is_null($this->endTime)) {
			$this->stop();
		}
		$array = get_object_vars($this);
		return json_encode($array);
	}
}