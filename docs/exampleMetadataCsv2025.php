<?php

/**
 * Example usage of parse-swissmetnet for OGD metadata 2025
 * 
 * @see <https://data.geo.admin.ch/browser/index.html#/collections/ch.meteoschweiz.ogd-smn>
 */

require __DIR__ . '/../vendor/autoload.php';

$metadata = [];

$locationsRaw = file_get_contents(__DIR__ . '/../tests/resources/validMetadata/2025/ogd-smn_meta_stations.csv');
$locationsMetadata = \cstuder\ParseSwissMetNet\MetadataParser::parse($locationsRaw);

$parametersRaw = file_get_contents(__DIR__ . '/../tests/resources/validMetadata/2025/ogd-smn_meta_parameters.csv');
$parametersMetadata = \cstuder\ParseSwissMetNet\MetadataParser::parse($parametersRaw);

var_dump($parametersMetadata); die();
$metadata = [
    'locations' => $locationsMetadata->locations,
    'parameters' => $parametersMetadata->parameters,
];
var_dump($metadata);
