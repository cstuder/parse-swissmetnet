# parse-swissmetnet

[![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active) ![PHPUnit tests](https://github.com/cstuder/parse-swissmetnet/workflows/PHPUnit%20tests/badge.svg)

Simple [PHP package](https://packagist.org/packages/cstuder/parse-swissmetnet) to parse SwissMetNet Open Data strings.

**Disclaimer:** This library is not official and not affiliated with MeteoSwiss.

Created for usage on [api.existenz.ch](https://api.existenz.ch) and indirectly on [Aare.guru](https://aare.guru). As of 2025 in productive use.

## SwissMetNet

Starting from may 2025, [MeteoSwiss](https://www.meteoschweiz.admin.ch/) (Bundesamt für Meteorologie und Klimatologie/Federal Office of Meteorology and Climatology) publishes their [SwissMetNet](https://www.meteoswiss.admin.ch/home/measurement-and-forecasting-systems/land-based-stations/automatisches-messnetz.html) measurement data on the new [OGD Portal](https://data.geo.admin.ch/browser/index.html#/collections/ch.meteoschweiz.ogd-smn). See their [Open Data Documentation](https://opendatadocs.meteoswiss.ch) for further information.

Measures air temperatures, rain rate/precipitation, wind, pressure, geopotentials, sunshine duration and more. Not every station measures every parameter.

Note that most stations measure this 2 meters above ground, but some tower stations locate their sensors higher in the air. The parameters with suffix `_tow` are measured on tower stations.

Periodicity: 10 minutes.

**Licencing restrictions apply by MeteoSwiss.** [CC-BY](https://creativecommons.org/licenses/by/4.0/). You will have to credit any usage of the data with the string "Source: MeteoSwiss". See the official [Terms of Use](https://opendatadocs.meteoswiss.ch/general/terms-of-use) for details.

### Getting the data

1. Go to the STAC browser for the [Automatic weather stations](https://data.geo.admin.ch/browser/index.html#/collections/ch.meteoschweiz.ogd-smn).
1. Download the assets for station and parameter metadata CSV files.
1. Go to the desired station.
1. Download the assets for the measurement data as CSV files.

There is a special file called `VQHA80.csv`only mentioned [in the documentation](https://opendatadocs.meteoswiss.ch/a-data-groundbased/a1-automatic-weather-stations?data-structure=one-file-with-all-stations). It contains last measured values for all stations for the main parameters. This file is updated every 10 minutes.

### Data format 2025

Starting from may 2025 the assets with historical data are published. They differ slightly again from the previous format: Different headers and a different time format:

```csv
station_abbr;reference_timestamp;tre200s0;tre005s0;tresurs0;xchills0;ure200s0;tde200s0;pva200s0;prestas0;pp0qnhs0;pp0qffs0;ppz850s0;ppz700s0;fkl010z1;fve010z0;fkl010z0;dkl010z0;wcc006s0;fu3010z0;fkl010z3;fu3010z1;fu3010z3;rre150z0;htoauts0;gre000z0;ods000z0;oli000z0;olo000z0;osr000z0;sre000z0
BER;01.02.2004 00:00;7.4;1.7;;;66.2;1.5;6.8;955.5;1022.3;1023.1;;;5.8;3;;258;;10.8;;20.9;;0;;6;;;;;0
BER;01.02.2004 00:10;7.6;1.8;;;65.7;1.6;6.9;955.5;1022.3;1023;;;7.9;3.6;;246;;13;;28.4;;0;;0;;;;;0
...
```

### Data format 2020

Starting from 2020-10-19 the data format of `VQHA80` changed slighty from the orginial CSV format: They are now valid CSV files, semicolon separated, with a new header line:

```csv
Station/Location;Date;tre200s0;rre150z0;sre000z0;gre000z0;ure200s0;tde200s0;dkl010z0;fu3010z0;fu3010z1;prestas0;pp0qffs0;pp0qnhs0;ppz850s0;ppz700s0;dv1towz0;fu3towz0;fu3towz1;ta1tows0;uretows0;tdetows0
TAE;202010191400;11.90;0.00;10.00;326.00;67.50;6.10;26.00;4.30;7.20;957.70;1021.00;1021.20;-;-;-;-;-;-;-;-
COM;202010191400;11.70;0.00;0.00;100.00;68.30;6.10;114.00;1.80;4.30;955.90;1023.60;1023.80;-;-;-;-;-;-;-;-
...
```

### Data format

MeteoSwiss data files `VQHA80` are semicolon separated CSVs with a custom header:

```csv
MeteoSchweiz / MeteoSuisse / MeteoSvizzera / MeteoSwiss

stn;time;tre200s0;rre150z0;sre000z0;gre000z0;ure200s0;tde200s0;dkl010z0;fu3010z0;fu3010z1;prestas0;pp0qffs0;pp0qnhs0;ppz850s0;ppz700s0;dv1towz0;fu3towz0;fu3towz1;ta1tows0;uretows0;tdetows0
TAE;201911222230;2.4;0.0;0;1;97;1.9;93;5.0;10.8;939.0;1003.3;1001.5;-;-;-;-;-;-;-;-
COM;201911222230;4.1;0.1;0;1;99;4.0;343;3.6;5.0;944.1;1012.9;1011.4;-;-;-;-;-;-;-;-
...
```

### Legay data format

Older data files like `VQHA69.csv` used the pipe symbol as separators:

```csv
MeteoSchweiz / MeteoSuisse / MeteoSvizzera / MeteoSwiss

stn|time|tre200s0|sre000z0|rre150z0|dkl010z0|fu3010z0|pp0qnhs0|fu3010z1|ure200s0|prestas0|pp0qffs0
TAE|201803301120|9.1|1|0.0|87|4.7|1000.1|10.4|73|937.6|1000.2
COM|201803301120|7.3|0|0.2|185|16.9|1005.2|26.6|94|938.3|1005.8
...
```

### Metadata format

The legacy metadata files are free form textual files with space separated tables. Good luck parsing those.

Encoding is ISO-8859-1. This library outputs UTF-8.

Starting from 2021, the metadata is split up into two files: A text file (I.e. `VQHA80_en.txt`) containing the parameter metadata and a link to a CSV (I.e. `ch.meteoschweiz.messnetz-automatisch_en.csv`) containing the location metadata.

Starting from 2025, all metadata is published as CSV files. (`ogd-smn_meta_stations.csv` and `ogd-smn_meta_parameters.csv`).

## Installation

`composer require cstuder/parse-swissmetnet`

## Example usage

```php
<?php

require('vendor/autoload.php');

$raw = file_get_contents('https://data.geo.admin.ch/ch.meteoschweiz.messwerte-aktuell/VQHA80.csv');

$data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

var_dump($data);
```

See the `bin` directory for more working code.

## Methods

The parser is intentionally limited: It parses the given string and returns all data which looks valid. It silently skips over any line it doesn't understand.

Values are converted to `float`. Missing data values are not returned, the values will never be `null`.

### `SuperParser::parse(string $raw)`

Parses a SwissMetNet data string, tries out all available parsers one after another. If any of them finds anything, returns that data.

Returns an empty row if no parsers find anything. Use at your own risk.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `DataParser2025::parse(string $raw)`

Parses a SwissMetNet data string containing semicolon separated measurements in the OGD 2025 version.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `DataParser2020::parse(string $raw)`

Parses a SwissMetNet data string containing semicolon separated measurements in the 2020 version.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `DataParser::parse(string $raw)`

Parses a SwissMetNet data string containing semicolon separated measurements.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `LegacyDataParser::parse(string $raw)`

Parses an older SwissMetNet data string containing pipe separated measurements.

Returns a row of value objects with the keys `timestamp`, `loc`, `par`, `val`.

### `MetadataParser::parse(string $raw)`

Parses a SwissMetNet description string containing location and parameter definitions.

Returns two fields: `locations` and `parameters`, both containing arrays of StdClass objects with fields such as location coordinates or parameter units.

This parse method behaves like the `SuperParser`: It tries parsing text files à la `VQHA80_en.txt` and CSV files à la `ch.meteoschweiz.messnetz-automatisch_en.txt`. It combines the found metadata into one list.

### `MetadataParser::parseFromTextFile(string $raw)`

Parses a SwissMetNet description string from a file like `VQHA80_en.txt` containing location and parameter definitions.

Returns two fields: `locations` and `parameters`, both containing arrays of StdClass objects with fields such as location coordinates or parameter units.

### `MetadataParser::parseFromCsvFile(string $raw)`

Parses a SwissMetNet description string from a file like `ogd-smn_meta_stations.csv` containing location and parameter definitions.

Returns two fields: `locations` and `parameters`, both containing arrays of StdClass objects with fields such as location coordinates or parameter units.

## Testing

Run `composer test` to execute the PHPUnit test suite.

## Releasing

1. Add changes to the [changelog](CHANGELOG.md).
1. Add new parsers to the `SuperParser`.
1. Create a new tag `vX.X.X`.
1. Push.

## License

MIT.

## Author

Christian Studer <cstuder@existenz.ch>, Bureau für digitale Existenz.
