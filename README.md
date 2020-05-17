# parse-swissmetnet

![PHPUnit tests](https://github.com/cstuder/parse-swissmetnet/workflows/PHPUnit%20tests/badge.svg)

Simple PHP package to parse SwissMetNet Open Data strings.

**Disclaimer:** This library is not official and not affiliated with MeteoSwiss.

Created for usage on [api.existenz.ch](https://api.existenz.ch) and indirectly on [Aare.guru](https://aare.guru). As of 2020 in productive use.

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

### Data format

MeteoSwiss data files are semicolon separated CSVs with a custom header:

```csv
MeteoSchweiz / MeteoSuisse / MeteoSvizzera / MeteoSwiss

stn;time;tre200s0;rre150z0;sre000z0;gre000z0;ure200s0;tde200s0;dkl010z0;fu3010z0;fu3010z1;prestas0;pp0qffs0;pp0qnhs0;ppz850s0;ppz700s0;dv1towz0;fu3towz0;fu3towz1;ta1tows0;uretows0;tdetows0
TAE;201911222230;2.4;0.0;0;1;97;1.9;93;5.0;10.8;939.0;1003.3;1001.5;-;-;-;-;-;-;-;-
COM;201911222230;4.1;0.1;0;1;99;4.0;343;3.6;5.0;944.1;1012.9;1011.4;-;-;-;-;-;-;-;-
...
```

#### Legay data format

Older data files like `VQHA69.csv` used the pipe symbol as separators:

```csv
MeteoSchweiz / MeteoSuisse / MeteoSvizzera / MeteoSwiss

stn|time|tre200s0|sre000z0|rre150z0|dkl010z0|fu3010z0|pp0qnhs0|fu3010z1|ure200s0|prestas0|pp0qffs0
TAE|201803301120|9.1|1|0.0|87|4.7|1000.1|10.4|73|937.6|1000.2
COM|201803301120|7.3|0|0.2|185|16.9|1005.2|26.6|94|938.3|1005.8
...
```

#### Metadata format

The metadata files are free form textual files with space separated tables. Good luck parsing those.

Encoding is ISO-8859-1.

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

The parser is intentionally limited: It parses the given string and returns all data which looks valid. It silently skips over any line it doesn't understand.

Values are converted to `float`. Missing values are returned as `null`.

### `DataParser::parse(string $raw)`

Parses a SwissMetNet data string containing semicolon separated measurements.

Returns an array of StdClass objects with the keys `timestamp`, `location`, `parameter`, `value`.

### `LegacyDataParser::parse(string $raw)`

Parses an older SwissMetNet data string containing pipe separated measurements.

Returns an array of StdClass objects with the keys `timestamp`, `location`, `parameter`, `value`.

### `MetadataParser::parse(string $raw)`

Parses a SwissMetNet description string containing location and parameter definitions.

Returns two arrays of StdClass objects: `locations` and `parameters`.

## Testing

`composer test` to execute the PHPUnit test suite.

## License

MIT.

## Author

Christian Studer <cstuder@existenz.ch>, Bureau für digitale Existenz.
