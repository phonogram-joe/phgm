<?php

// log level
define('LOG_TRACE', 1);
define('LOG_DEBUG', 2);
define('LOG_INFO', 3);
define('LOG_WARN', 4);
define('LOG_ERROR', 5);
define('LOG_FATAL', 6);

define('LOGGER_LEVEL', LOG_WARN);
define('LOGGER_FILE', LOG_DIR . DS . 'system.log');