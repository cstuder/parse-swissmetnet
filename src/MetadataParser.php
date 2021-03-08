<?php

namespace cstuder\ParseSwissMetNet;

/**
 * Parser for SwissMetNet meta data strings
 */
class MetadataParser
{
    /**
     * Parse metadata from different file formats
     * 
     * @param string $raw SwissMetNet metadata string from text file or CSV
     * @return stdClass $metadata[array locations, array parameters]
     */
    public static function parse(string $raw)
    {
        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        $metadataFromTextFile = self::parseFromTextFile($raw);
        $metadataFromCsvFile = self::parseFromCsvFile($raw);

        $metadata->locations = array_merge($metadataFromTextFile->locations, $metadataFromCsvFile->locations);
        $metadata->parameters = array_merge($metadataFromTextFile->parameters, $metadataFromCsvFile->parameters);

        return $metadata;
    }

    /**
     * Parse metadata from text file
     * 
     * Legacy: Both parameter and location metadata were stored in VQHA80/98 files.
     * 
     * As of 2021 only parameter metadata is stored in text files. Location metadata is stored
     * in CSV files.
     * 
     * @param string $raw SwissMetNet metadata string from text file
     * @return stdClass $metadata[array locations, array parameters]
     */
    public static function parseFromTextFile(string $raw)
    {
        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        $lines = explode("\n", $raw);

        // Find locations
        foreach ($lines as $line) {
            // Query for station line
            $parts = [];
            if (!preg_match("/^([A-Z]{3,5})[ ]+(.+)[ ]+([0-9]{1,2})°([0-9]{2})'\/([0-9]{1,2})°([0-9]{2})'[ ]+([0-9]+)\/([0-9]+)[ ]+([0-9]+)/", utf8_encode($line), $parts)) continue;

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

    /**
     * Parse metadata from CSV file
     * 
     * Legacy: Both parameter and location metadata were stored in VQHA80/98 files.
     * 
     * As of 2021 only parameter metadata is stored in text files. Location metadata is stored
     * in CSV files.
     * 
     * @param string $raw SwissMetNet metadata string from CSV file
     * @return stdClass $metadata[array locations, array parameters]
     */
    public static function parseFromCsvFile(string $raw)
    {
        $separator = ';';

        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        if (substr($raw, 0, 10) != '"Station";') {
            // Probably not the CSV we're looking for
            return $metadata;
        }

        $lines = explode("\n", $raw);

        $headers = str_getcsv(array_shift($lines), $separator);

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                break;
            }

            $fields = str_getcsv($line, $separator);

            $location = [];

            foreach ($fields as $index => $value) {
                switch ($headers[$index]) {
                    default:
                        // Unknown header, do nothing.
                        break;

                    case "Station":
                        $location['name'] = utf8_encode($value);
                        break;

                    case "Abbr.":
                        $location['id'] = $value;
                        break;

                    case "WIGOS-ID":
                        $location['wigos-id'] = $value ? $value : null;
                        break;

                    case "Station type":
                        $location['station-type'] = $value;
                        break;

                    case "Data Owner":
                        $location['data-owner'] = $value;
                        break;

                    case "Data since":
                        $location['data-since'] = $value;
                        break;

                    case "Station height m. a. sea level":
                        $location['alt'] = intval($value);
                        break;

                    case "Barometric altitude m. a. ground":
                        $location['alt-barometric'] = $value ? intval($value) : null;
                        break;

                    case "CoordinatesE":
                        $location['chx'] = intval($value);
                        break;

                    case "CoordinatesN":
                        $location['chy'] = intval($value);
                        break;

                    case "Latitude":
                        $location['lat'] = floatval($value);
                        break;

                    case "Longitude":
                        $location['lon'] = floatval($value);
                        break;

                    case "Canton":
                        $location['canton'] = $value;
                        break;

                    case "Measurements":
                        $location['measurements'] = $value;
                        break;

                    case "Link":
                        $location['link'] = $value;
                        break;
                }
            }

            $metadata->locations[] = $location;
        }

        return $metadata;
    }
}
