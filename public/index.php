<?php

if (PHP_MAJOR_VERSION < 8) {
    echo 'Php version less than 8';
    die;
}

require_once dirname(__DIR__) . '/config/init.php';
require_once FUNC . '/debug.php';
require_once FUNC . '/auxiliarys.php';

session_start();
new \fa\App();
