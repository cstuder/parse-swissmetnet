#!/usr/bin/env php
<?php
require(__DIR__ . '/../vendor/autoload.php');

$rawparameters = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.ogd-smn/ogd-smn_meta_parameters.csv');

$parameters = \cstuder\ParseSwissMetNet\MetadataParser::parse($rawparameters);

var_dump($parameters);


$rawlocations = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.ogd-smn/ogd-smn_meta_stations.csv');

$locations = \cstuder\ParseSwissMetNet\MetadataParser::parse($rawlocations);

var_dump($locations);
