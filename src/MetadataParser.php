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
     * As of 2025 both parameter and location metadata are stored in CSV files.
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
        $csvIdentifiers = [
            '"Station"',
            '"Stazione"',
            'station_abbr',
            'parameter_shortname',
        ];

        $metadata = new \stdClass();
        $metadata->locations = [];
        $metadata->parameters = [];

        $isCsv = false;
        foreach ($csvIdentifiers as $identifier) {
            if (str_starts_with($raw, $identifier)) {
                // Found a CSV file with the expected identifiers
                $isCsv = true;
                break;
            }
        }

        if (!$isCsv) {
            // Not a CSV file, return empty metadata
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
            $parameter = [];

            foreach ($fields as $index => $value) {
                switch (self::iso_decode($headers[$index])) {
                    default:
                        // Unknown header
                        throw new \UnexpectedValueException('Unknown header column: ' . self::iso_decode($headers[$index]));
                        break;

                    // Location metadata

                    case "Station":
                    case "Stazione":
                    case "station_name":
                        $location['name'] = self::iso_decode($value);
                        break;

                    case "Abbr.":
                    case "Abk.":
                    case "Abr.":
                    case "station_abbr":
                        $location['id'] = $value;
                        break;

                    case "WIGOS-ID":
                    case "station_wigos_id":
                        $location['wigos-id'] = $value ? $value : null;
                        break;

                    case "Station type":
                    case "Stationstyp":
                    case "Type de station":
                    case "Tipo di stazione":
                    case "station_type_en":
                        $location['station-type'] = $value;
                        break;

                    case "station_type_de":
                        $location['station-type-de'] = self::iso_decode($value);
                        break;

                    case "station_type_fr":
                        $location['station-type-fr'] = self::iso_decode($value);
                        break;

                    case "station_type_it":    
                        $location['station-type-it'] = self::iso_decode($value);
                        break;

                    case "Data Owner":
                    case "Eigentümer":
                    case "Propriétaire ":
                    case "Proprietario ":
                    case "station_dataowner":
                        $location['data-owner'] = $value;
                        break;

                    case "Data since":
                    case "Daten seit":
                    case "Données depuis":
                    case "Dati dal":
                    case "station_data_since":
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
                    case "station_height_masl":
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
                    case "station_height_barometer_masl":
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

                    case "station_coordinates_lv95_east":
                        $location['chx-lv95'] = intval($value);
                        break;

                    case "station_coordinates_lv95_north":
                        $location['chy-lv95'] = intval($value);
                        break;

                    case "Latitude":
                    case "Breitengrad":
                    case "Latitudine":
                    case "station_coordinates_wgs84_lat":
                        $location['lat'] = floatval($value);
                        break;

                    case "Longitude":
                    case "Längengrad":
                    case "Longitudine ": // Trailing space intentional
                    case "station_coordinates_wgs84_lon":
                        $location['lon'] = floatval($value);
                        break;

                    case "Canton":
                    case "Kanton":
                    case "Cantone":
                    case "station_canton":
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
                    case "station_url_en":
                        $location['link'] = $value;
                        break;

                    case "station_url_de":
                        $location['link-de'] = self::iso_decode($value);
                        break;

                    case "station_url_fr":
                        $location['link-fr'] = self::iso_decode($value);
                        break;

                    case "station_url_it":
                        $location['link-it'] = self::iso_decode($value);
                        break;

                    case "Exposition":
                    case "Esposizione":
                    case "station_exposition_en":
                        $location['exposition'] = self::iso_decode($value);
                        break;

                    case "station_exposition_de":
                        $location['exposition-de'] = self::iso_decode($value);
                        break;

                    case "station_exposition_fr":
                        $location['exposition-fr'] = self::iso_decode($value);
                        break;

                    case "station_exposition_it":
                        $location['exposition-it'] = self::iso_decode($value);
                        break;

                    // Parameter metadata

                    case "parameter_shortname":
                        $parameter['id'] = $value;
                        break;

                    case "parameter_description_en":
                        $parameter['description'] = self::iso_decode($value);
                        break;

                    case "parameter_description_de":
                        $parameter['description-de'] = self::iso_decode($value);
                        break;

                    case "parameter_description_fr":
                        $parameter['description-fr'] = self::iso_decode($value);
                        break;

                    case "parameter_description_it":
                        $parameter['description-it'] = self::iso_decode($value);
                        break;

                    case "parameter_group_en":
                        $parameter['group'] = self::iso_decode($value);
                        break;

                    case "parameter_group_de":
                        $parameter['group-de'] = self::iso_decode($value);
                        break;

                    case "parameter_group_fr":
                        $parameter['group-fr'] = self::iso_decode($value);
                        break;

                    case "parameter_group_it":
                        $parameter['group-it'] = self::iso_decode($value);
                        break;

                    case "parameter_granularity":
                        $parameter['granularity'] = self::iso_decode($value);
                        break;

                    case "parameter_decimals":
                        $parameter['decimals'] = intval($value);
                        break;

                    case "parameter_datatype":
                        $parameter['datatype'] = self::iso_decode($value);
                        break;

                    case "parameter_unit":
                        $parameter['unit'] = self::iso_decode($value);
                        break;
                }
            }

            if (!empty($location)) {
                // Add location to metadata
                $metadata->locations[] = (object) $location;
            }

            if (!empty($parameter)) {
                // Add parameter to metadata
                $metadata->parameters[] = (object) $parameter;
            }
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
