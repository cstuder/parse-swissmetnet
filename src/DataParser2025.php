<?php

namespace cstuder\ParseSwissMetNet;

/**
 * Parser for SwissMetNet data strings OGD 2025
 */
class DataParser2025 extends DataParserBase
{
    protected const LOCATION_FIELD_NAME = 'station_abbr';
    protected const DATETIME_FIELD_NAME = 'reference_timestamp';
}
