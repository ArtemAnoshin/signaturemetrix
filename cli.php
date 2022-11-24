<?php

require_once 'functions/functions.php';
require_once 'classes/SigmxScanner.php';

$path_empty = false;
$path_valid = true;
$scan_result = false;
const SIGMX_ROOT_PATH = __DIR__;
define('WP_ROOT_PATH', dirname(SIGMX_ROOT_PATH));

/**
 * Stage: 1
 * Checking path
 */
if (!empty($argv[1])) {
    $path_valid = sigmx__path_validation($argv[1]);
}

/**
 * Stage: 2
 * Start signature scanner
 */
if ($path_valid) {
    $sigmx_scanner = new SigmxScanner($argv[1]);
    $scan_result = $sigmx_scanner->getResult();
    if ($scan_result) {
        echo 'OK' . PHP_EOL;
    } else {
        echo 'ERROR' . PHP_EOL;
    }
} else {
    echo 'ERROR' . PHP_EOL;
}
