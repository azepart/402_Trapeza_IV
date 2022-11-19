<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once(__DIR__ . '/../vendor/autoload.php');
} else {
    require_once(__DIR__ . '/../../../autoload.php');
}

use function trapezaiv\hangman\Controller\menu;
use function trapezaiv\hangman\Controller\menuReplay;

if (isset($argv[1]) && isset($argv[2])) {
    menuReplay($argv[1], $argv[2]);
} elseif (isset($argv[1])) {
    menu($argv[1]);
} else {
    menu("-n");
}
