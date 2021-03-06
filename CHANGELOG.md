# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.2] - 2018-03-13

- Fixes the return type of locations objects to `StdClass`.

## [1.2.1] - 2018-03-13

- Adds german, french and italian metadata parsing for th enew CSV location files as well.

## [1.2.0] - 2021-03-08

- Adds metadata parsing for the new CSV location files. (English only)
- Note that the new CSV location files contain all automated measurement stations of MeteoSwiss, including the weather stations and precipitation stations.
- Also note that the Swiss coordinates in the CSV files are in the new LV95 system, instead of LV03. (Details: [Swiss coordinates system](https://en.wikipedia.org/wiki/Swiss_coordinate_system))

## [1.1.0] - 2020-10-20

- Adds `DataParser2020` to handle new data format.
- Adds `SuperParser` for forward compatibility.

## [1.0.0] - 2020-05-30

- Initial release (Mature code has been copied over from a non-modularized codebase.)
