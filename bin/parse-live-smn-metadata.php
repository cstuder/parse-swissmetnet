#!/usr/bin/env php
<?php
require(__DIR__ . '/../vendor/autoload.php');

$rawparameters = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.messwerte-aktuell/info/VQHA80_de.txt');

$parameters = \cstuder\ParseSwissMetNet\MetadataParser::parse($rawparameters);

var_dump($parameters);


$rawlocations = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.messnetz-automatisch/ch.meteoschweiz.messnetz-automatisch_de.csv');

$locations = \cstuder\ParseSwissMetNet\MetadataParser::parse($rawlocations);

var_dump($locations);
