<?php

use PHPUnit\Framework\TestCase;

/**
 * Quantitative tests of the super parser
 */
class SuperParserTest extends TestCase
{
    public function testParseDataVQHA80_2020()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA80_2020.csv');
        $data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

        $this->assertEquals(1734, $data->getCount());
        $this->assertEquals(20, count($data->getParameters()));
        $this->assertEquals(158, count($data->getLocations())); // Location CDM is inoperative
        $this->assertEquals(1, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testParseDataVQHA80()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA80.csv');
        $data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

        $this->assertEquals(1740, $data->getCount());
        $this->assertEquals(20, count($data->getParameters()));
        $this->assertEquals(159, count($data->getLocations()));
        $this->assertEquals(1, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testParseDataVQHA98()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA98.csv');
        $data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

        $this->assertEquals(127, $data->getCount());
        $this->assertEquals(1, count($data->getParameters()));
        $this->assertEquals(127, count($data->getLocations()));
        $this->assertEquals(1, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testParseLegacyDataVQHA69()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validLegacyData/VQHA69.csv');
        $data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

        $this->assertEquals(1038, $data->getCount());
        $this->assertEquals(10, count($data->getParameters()));
        $this->assertEquals(114, count($data->getLocations()));
        $this->assertEquals(1, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }

    public function testParseOgdData2025()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/ogd-smn_ber_t_historical_2000-2009_shortened.csv');
        $data = \cstuder\ParseSwissMetNet\SuperParser::parse($raw);

        $this->assertEquals(64, $data->getCount());
        $this->assertEquals(16, count($data->getParameters()));
        $this->assertEquals(1, count($data->getLocations()));
        $this->assertEquals(4, count($data->getTimestamps()));

        $this->assertContainsOnlyInstancesOf('cstuder\ParseValueholder\Value', $data->getValues());
    }
}
