#!/usr/bin/env php
<?php
require(__DIR__ . '/../vendor/autoload.php');

$raw = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.messwerte-aktuell/VQHA80.csv');

$data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

var_dump($data);
