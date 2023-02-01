<?php

require_once 'DataParserTestCase.php';

use PHPUnit\Framework\DataParserTestCase;

/**
 * Quantitative tests of data parsers
 */
class DataParserTest extends DataParserTestCase
{
    public function testParseDataVQHA80_2020()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA80_2020.csv');
        $data = \cstuder\ParseSwissMetNet\DataParser2020::parse($raw);

        $this->assertEquals(1734, count($data->values));
        $this->assertEquals(20, count($this->collectParameters($data)));
        $this->assertEquals(158, count($this->collectLocations($data))); // Location CDM is inoperative
        $this->assertEquals(1, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }

    public function testParseDataVQHA80()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA80.csv');
        $data = \cstuder\ParseSwissMetNet\DataParser::parse($raw);

        $this->assertEquals(1740, count($data->values));
        $this->assertEquals(20, count($this->collectParameters($data)));
        $this->assertEquals(159, count($this->collectLocations($data)));
        $this->assertEquals(1, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }

    public function testParseDataVQHA98()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA98.csv');
        $data = \cstuder\ParseSwissMetNet\DataParser::parse($raw);

        $this->assertEquals(127, count($data->values));
        $this->assertEquals(1, count($this->collectParameters($data)));
        $this->assertEquals(127, count($this->collectLocations($data)));
        $this->assertEquals(1, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }

    public function testParseLegacyDataVQHA69()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validLegacyData/VQHA69.csv');
        $data = \cstuder\ParseSwissMetNet\LegacyDataParser::parse($raw);

        $this->assertEquals(1038, count($data->values));
        $this->assertEquals(10, count($this->collectParameters($data)));
        $this->assertEquals(114, count($this->collectLocations($data)));
        $this->assertEquals(1, count($this->collectTimestamps($data)));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->values);
    }
}
