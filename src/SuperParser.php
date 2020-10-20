<?php

namespace cstuder\ParseSwissMetNet;

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
     * @return array
     */
    public static function parse(string $raw)
    {
        // Try DataParser2020
        $data = DataParser2020::parse($raw);

        if (!empty($data)) {
            return $data;
        }

        // Try DataParser
        $data = DataParser::parse($raw);

        if (!empty($data)) {
            return $data;
        }

        // Try LegacyDataParser
        $data = LegacyDataParser::parse($raw);

        if (!empty($data)) {
            return $data;
        }

        return [];
    }
}
