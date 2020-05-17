<?php

/**
 * Example usage of parse-swissmetnet
 */

require __DIR__ . '/../vendor/autoload.php';

$raw = file_get_contents(__DIR__ . '/../tests/resources/validLegacyData/VQHA69.csv');

$data = \cstuder\ParseSwissMetNet\LegacyDataParser::parse($raw);

var_dump($data);
