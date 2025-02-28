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
            if (!preg_match("/^([A-Z]{3,5})[ ]+(.+)[ ]+([0-9]{1,2})°([0-9]{2})'\/([0-9]{1,2})°([0-9]{2})'[ ]+([0-9]+)\/([0-9]+)[ ]+([0-9]+)/", self::iso_decode($line), $parts)) continue;

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
            if (!preg_match("/^([a-z0-9]{8})[ ]{5,}(.*)[ ]{5,}(.*)/", self::iso_decode($line), $parts)) continue;

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
     * @throws UnexpectedValueException
     */
    public static function parseFromCsvFile(string $raw)
    {
        $separator = ';';
        $enclosure = '"';
        $escape = '';

        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        if (substr($raw, 0, 10) != '"Station";' && substr($raw, 0, 11) != '"Stazione";') {
            // Probably not the CSV we're looking for
            return $metadata;
        }

        $lines = explode("\n", $raw);

        $headers = str_getcsv(array_shift($lines), $separator, $enclosure, $escape);

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                break;
            }

            $fields = str_getcsv($line, $separator, $enclosure, $escape);

            $location = [];

            foreach ($fields as $index => $value) {
                switch (self::iso_decode($headers[$index])) {
                    default:
                        // Unknown header
                        throw new \UnexpectedValueException('Unknown header column: ' . self::iso_decode($headers[$index]));
                        break;

                    case "Station":
                    case "Stazione":
                        $location['name'] = self::iso_decode($value);
                        break;

                    case "Abbr.":
                    case "Abk.":
                    case "Abr.":
                        $location['id'] = $value;
                        break;

                    case "WIGOS-ID":
                        $location['wigos-id'] = $value ? $value : null;
                        break;

                    case "Station type":
                    case "Stationstyp":
                    case "Type de station":
                    case "Tipo di stazione":
                        $location['station-type'] = $value;
                        break;

                    case "Data Owner":
                    case "Eigentümer":
                    case "Propriétaire ":
                    case "Proprietario ":
                        $location['data-owner'] = $value;
                        break;

                    case "Data since":
                    case "Daten seit":
                    case "Données depuis":
                    case "Dati dal":
                        $location['data-since'] = $value;
                        break;

                    case "Station height m. a. sea level": // Old
                    case "Stationshöhe m. ü. M.":
                    case "Altitude station m. s. mer":
                    case "Altitudine stazione m slm":
                    case "Station height m a. sea level": // New
                    case "Stationshöhe m ü. M.":
                    case "Altitude station m s. mer":
                    case "Altitudine stazione m slm":
                        $location['alt'] = intval($value);
                        break;

                    case "Barometric altitude m. a. ground": // Old
                    case "Barometerhöhe m. ü. Boden":
                    case "Altitude du baromètre m. s. sol":
                    case "Altitudine del barometro m. da terra":
                    case "Barometric altitude m a. ground": // New
                    case "Barometerhöhe m ü. Boden":
                    case "Altitude du baromètre m s. sol":
                    case "Altitudine del barometro m da terra":
                        $location['alt-barometric'] = $value ? intval($value) : null;
                        break;

                    case "KoordinatenE":
                    case "CoordinatesE":
                    case "CoordonnéesE":
                    case "CoordinateE":
                        $location['chx'] = intval($value);
                        break;

                    case "CoordinatesN":
                    case "KoordinatenN":
                    case "CoordonnéesN":
                    case "CoordinateN":
                        $location['chy'] = intval($value);
                        break;

                    case "Latitude":
                    case "Breitengrad":
                    case "Latitudine":
                        $location['lat'] = floatval($value);
                        break;

                    case "Longitude":
                    case "Längengrad":
                    case "Longitudine ":
                        $location['lon'] = floatval($value);
                        break;

                    case "Canton":
                    case "Kanton":
                    case "Cantone":
                        $location['canton'] = $value;
                        break;

                    case "Measurements":
                    case "Messungen":
                    case "Mesures":
                    case "Misurazioni":
                        $location['measurements'] = $value;
                        break;

                    case "Link":
                    case "Lien":
                    case "Collegamento":
                        $location['link'] = $value;
                        break;

                    case "Exposition":
                    case "Esposizione":
                        $location['exposition'] = $value;
                        break;
                }
            }

            $metadata->locations[] = (object) $location;
        }

        return $metadata;
    }

    /**
     * Convert ISO-8859-1 string to UTF-8
     * 
     * @param string $string The string to be converted
     * @return string The converted string
     */
    private static function iso_decode(string $string): string
    {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }
}
