<?php

/**
 * Example usage of parse-swissmetnet
 */

require __DIR__ . '/../vendor/autoload.php';

$raw = file_get_contents(__DIR__ . '/../tests/resources/validMetadata/2021/VQHA80_en.txt');

$metadata = \cstuder\ParseSwissMetNet\MetadataParser::parse($raw);

var_dump($metadata);
