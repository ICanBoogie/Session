<?php

namespace ICanBoogie;

use ICanBoogie\Session\RuntimeSessionHandler;

use function ob_start;

require __DIR__ . '/../vendor/autoload.php';

ini_set('session.use_cookies', '0');

RuntimeSessionHandler::register();

ob_start(); // Prevents PHPUnit from sending headers
