<?php

namespace cstuder\ParseSwissMetNet;

use cstuder\ParseValueholder\Row;

/**
 * Super Parser for SwissMetNet data strings
 */
class SuperParser
{
    /**
     * Parse data string
     * 
     * Tries multiple different parsers until it successfully parses anything.
     * 
     * Fails silently when nothing is found or understood. Use with caution.
     * 
     * @param string $raw SwissMetNet data string
     * @return Row Parsed data
     */
    public static function parse(string $raw)
    {
        // Try DataParser2020
        $data = DataParser2020::parse($raw);

        if (!empty($data->getValues())) {
            return $data;
        }

        // Try DataParser
        $data = DataParser::parse($raw);

        if (!empty($data->getValues())) {
            return $data;
        }

        // Try LegacyDataParser
        $data = LegacyDataParser::parse($raw);

        if (!empty($data->getValues())) {
            return $data;
        }

        return new Row();
    }
}
