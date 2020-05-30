<?php

namespace cstuder\ParseSwissMetNet;

/**
 * Parser for SwissMetNet meta data strings
 */
class MetadataParser
{
    public static function parse(string $raw)
    {
        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        $lines = explode("\n", $raw);

        // Find locations
        foreach ($lines as $line) {
            // Query for station line
            $parts = [];
            if (!preg_match("/^([A-Z]{3})[ ]+(.+)[ ]+([0-9]{1,2})°([0-9]{2})'\/([0-9]{1,2})°([0-9]{2})'[ ]+([0-9]+)\/([0-9]+)[ ]+([0-9]+)/", utf8_encode($line), $parts)) continue;

            $location = [
                'id' => $parts[1],
                'name' => trim($parts[2]),
                'lat' => round($parts[5] + ($parts[6] / 60), 2),
                'lon' => round($parts[3] + ($parts[4] / 60), 2),
                'chx' => intval($parts[7]),
                'chy' => intval($parts[8]),
                'alt' => intval($parts[9]),
            ];

            $metadata->locations[] = (object) $location;
        }

        // Find parameters
        foreach ($lines as $line) {
            // Query for parameter line
            $parts = [];
            if (!preg_match("/^([a-z0-9]{8})[ ]{5,}(.*)[ ]{5,}(.*)/", utf8_encode($line), $parts)) continue;

            $parameter = [
                'id' => $parts[1],
                'unit' => trim($parts[2]),
                'description' => trim($parts[3]),
            ];

            $metadata->parameters[] = (object) $parameter;
        }

        return $metadata;
    }
}
