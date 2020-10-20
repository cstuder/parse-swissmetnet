<?php

namespace cstuder\ParseSwissMetNet;

/**
 * Parser for SwissMetNet data strings V2020
 */
class DataParser2020 extends DataParserBase
{
    protected const LOCATION_FIELD_NAME = 'Station/Location';
    protected const DATETIME_FIELD_NAME = 'Date';
}
