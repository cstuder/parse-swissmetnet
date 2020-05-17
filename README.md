# parse-swissmetnet

Simple PHP package to parse SwissMetNet Open Data strings.

**Disclaimer:** This library is not official and not affiliated with MeteoSwiss.

Created for usage on [api.existenz.ch](https://api.existenz.ch) and indirectly on [Aare.guru](https://aare.guru).

## SwissMetNet

[MeteoSwiss](https://www.meteoschweiz.admin.ch/) (Bundesamt für Meteorologie und Klimatologie/Federal Office of Meteorology and Climatology) offers a selection of their [SwissMetNet](https://www.meteoswiss.admin.ch/home/measurement-and-forecasting-systems/land-based-stations/automatisches-messnetz.html) data on the [opendata.swiss portal](https://opendata.swiss/en/dataset/automatische-wetterstationen-aktuelle-messwerte).

Measures air temperatures, rain rate, winds, pressure, geopotentials and sunshine duration.

Note that most stations measure this 2 meters above ground, but some tower stations locate their sensors higher in the air. The parameters with suffix `_tow` are measured on tower stations.

Periodicity: 10 minutes.

**Licencing restrictions apply by MeteoSwiss.** See the Open Data download for information.

### Getting the data

1. Download the ZIP archive from the [Open Data portal](https://opendata.swiss/en/dataset/automatische-wetterstationen-aktuelle-messwerte).
2. Read the legal and licencing information.
3. Open `1_download_URL.txt`.
4. Find the links to the data CSVs (I.e. `VQHA80.csv` or `VQHA98.csv` and metadata TXTs.

## Installation

`composer require cstuder/parse-swissmetnet`

## Example usage

```php
<?php

require('vendor/autoload.php');

$raw = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.messwerte-aktuell/VQHA80.csv');

$data = \cstuder\ParseSwissMetNet\DataParser::parse($raw);

var_dump($data);
```

## Methods

### `DataParser::parse(string $raw)`

Parses a SwissMetNet data string containing measurements.

Returns an array of StdClass objects with the keys `timestamp`, `location`, `parameter`, `value`.

### `MetadataParser::parse(string $raw)`

Parses a SwissMetNet file description string containing location and parameter definitions.

Returns two arrays of StdClass objects: `locations` and `parameters`.

## Testing

`composer run test` to execute the PHPUnit test suite.

## License

MIT.

## Author

Christian Studer <cstuder@existenz.ch>, Bureau für digitale Existenz.
