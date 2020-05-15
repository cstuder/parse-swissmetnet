# parse-swissmetnet

Simple PHP package to parse SwissMetNet Open Data files.

## SwissMetNet

[MeteoSwiss](https://www.meteoschweiz.admin.ch/) (Bundesamt für Meteorologie und Klimatologie/Feder Office of Meteorology and Climatology) offers a selection of their [SwissMetNet](https://www.meteoswiss.admin.ch/home/measurement-and-forecasting-systems/land-based-stations/automatisches-messnetz.html) data on the [opendata.swiss portal](https://opendata.swiss/en/dataset/automatische-wetterstationen-aktuelle-messwerte).

Measures air temperatures, rain rate, winds, pressure, geopotentials and sunshine duration.

Note that most stations measure this 2 meters above ground, but some tower stations locate their sensors higher in the air. The parameter names with suffix `_tow` are measured on tower stations.

Periodicity: 10 minutes.

**Licencing restrictions apply by MeteoSwiss.** See the Open Data download for information.

## Installation

`composer require cstuder/parse-swissmetnet`

## Example usage

```php
<?php

require('vendor/autoload.php');

$raw = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.messwerte-aktuell/VQHA80.csv');

$data = \cstuder\parse-swissmetnet\Parser::parseData($raw);

var_dump($data);
```

## Methods

### parseData()

Parses a SwissMetNet data string containing measurements.

Returns an array of StdClass objects with the keys `timestamp`, `location`, `parameter`, `value`.

### parseMetaData()

Parses a SwissMetNet file description string containing location and parameter definitions.

Returns two arrays of StdClass objects: `locations` and `parameters`.

## Testing

`phpunit parserTests`

## License

MIT.

## Author

Christian Studer <cstuder@existenz.ch>, Bureau für digitale Existenz.

For usage on [api.existenz.ch](https://api.existenz.ch).
