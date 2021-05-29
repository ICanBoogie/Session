<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie;

use ICanBoogie\Session\RuntimeSessionHandler;

use function ob_start;

require __DIR__ . '/../vendor/autoload.php';

ini_set('session.use_cookies', '0');

RuntimeSessionHandler::register();

ob_start(); // Prevents PHPUnit from sending headers
