<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

/*
 * @file		index.php
 * @brief		
 * @author		Joe Savona
 * @date		2011.06.15
 */

// アプリケーション設定
define('PHGM_START_TIME', microtime(true));
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'webapp2012' . DIRECTORY_SEPARATOR . 'phgm.php');

phgm::go();

exit;
