<?php

namespace cstuder\ParseSwissMetNet;

/**
 * Abstract base class for data parsers
 */
abstract class DataParserBase
{
    protected const SEPARATOR = ';';
    protected const LOCATION_FIELD_NAME = 'stn';
    protected const DATETIME_FIELD_NAME = 'time';
    protected const TIMEZONE = 'UTC';
    protected const MISSING_VALUE_STRING = '-';

    /**
     * Parse data string
     * 
     * @param string $raw SwissMetNet data string
     * @return array
     */
    public static function parse(string $raw)
    {
        $data = [];

        // Parse data
        $indices = [];
        foreach (explode("\n", $raw) as $line) {
            $line = trim($line);

            // Skip over empty lines
            if ($line == '') {
                continue;
            }

            $parts = explode(static::SEPARATOR, $line);

            // Handle indices (Hopefully they come first)
            if ($parts[0] == static::LOCATION_FIELD_NAME) {
                $indices = $parts;
                continue;
            }

            // Handle non-value lines
            if (count($parts) != count($indices)) {
                continue;
            }

            // Finally a value line
            $location = null;
            $timestamp = null;
            foreach ($parts as $index => $value) {
                $column = $indices[$index];

                switch ($column) {
                    case static::LOCATION_FIELD_NAME:
                        // Store current location
                        $location = $value;
                        break;

                    case static::DATETIME_FIELD_NAME:
                        // Store current timestamp parsed from date time string, example: "202005161230"
                        $timestamp = strtotime("{$value} " . static::TIMEZONE);
                        break;

                    default:
                        // At this point the location and timestamp are known, so store the value if it isn't missing
                        if ($value == static::MISSING_VALUE_STRING) {
                            break;
                        }

                        $floatValue = floatval($value);

                        // All good, insert value
                        $data[] = (object) ([
                            'timestamp' => $timestamp,
                            'loc' => $location,
                            'par' => $column,
                            'val' => $floatValue
                        ]);

                        break;
                }
            }
        }

        return $data;
    }
}
