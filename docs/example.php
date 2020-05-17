<?php

/**
 * Example usage of parse-swissmetnet
 */

require(__DIR__ . '/../vendor/autoload.php');

$raw = file_get_contents(__DIR__ . '/../tests/resources/validData/VQHA80.csv');

$data = \cstuder\ParseSwissMetNet\DataParser::parse($raw);

var_dump($data);
