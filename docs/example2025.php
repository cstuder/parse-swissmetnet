<?php

/**
 * Example usage of parse-swissmetnet for OGD data 2025
 */

require __DIR__ . '/../vendor/autoload.php';

$raw = file_get_contents(__DIR__ . '/../tests/resources/validData/ogd-smn_ber_t_historical_2000-2009_shortened.csv');

$data = \cstuder\ParseSwissMetNet\DataParser2025::parse($raw);

var_dump($data);
