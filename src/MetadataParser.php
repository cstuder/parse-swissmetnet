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

        if (substr($raw, 0, 10) != '"Station";' && substr($raw, 0, 11) != '"Stazione";') {
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
                switch (utf8_encode($headers[$index])) {
                    default:
                        // Unknown header, do nothing.
                        var_dump($line, $headers[$index]);
                        throw new \Exception(utf8_encode($headers[$index])); // TODO remove this.
                        break;

                    case "Station":
                    case "Stazione":
                        $location['name'] = utf8_encode($value);
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
                    case "Propriétaire ": // TODO fixme
                    case "Proprietario ":
                        $location['data-owner'] = $value;
                        break;

                    case "Data since":
                    case "Daten seit":
                    case "Données depuis":
                    case "Dati dal":
                        $location['data-since'] = $value;
                        break;

                    case "Station height m. a. sea level":
                    case "Stationshöhe m. ü. M.":
                    case "Altitude station m. s. mer":
                    case "Altitudine stazione m slm":
                        $location['alt'] = intval($value);
                        break;

                    case "Barometric altitude m. a. ground":
                    case "Barometerhöhe m. ü. Boden":
                    case "Altitude du baromètre m. s. sol":
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
                }
            }

            $metadata->locations[] = (object) $location;
        }

        return $metadata;
    }
}
