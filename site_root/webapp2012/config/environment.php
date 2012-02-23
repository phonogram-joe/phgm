<?php

//	環境のオプション
define('ENVIRONMENT_TEST', 'test');
define('ENVIRONMENT_DEVELOPMENT', 'dev');
define('ENVIRONMENT_PRODUCTION', 'prod');

//	有効の環境。設定がこれによって変わる
define('ENVIRONMENT', ENVIRONMENT_DEVELOPMENT);