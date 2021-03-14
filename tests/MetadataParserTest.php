<?php

use PHPUnit\Framework\TestCase;

/**
 * Quantitative tests of metadata parsers
 */
class MetadataParserTest extends TestCase
{
    public function testParseMetadataVQHA80_2021_Version()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validMetadata/2021/VQHA80_en.txt');
        $data = \cstuder\ParseSwissMetNet\MetadataParser::parse($raw);

        $this->assertEquals(0, count($data->locations));
        $this->assertEquals(20, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }

    public function testParseMetadataMessnetz_Automatisch_2021_Version()
    {
        foreach (['en', 'de', 'fr', 'it'] as $lang) {
            $raw = file_get_contents(__DIR__ . "/resources/validMetadata/2021/ch.meteoschweiz.messnetz-automatisch_{$lang}.csv");
            $data = \cstuder\ParseSwissMetNet\MetadataParser::parse($raw);

            $this->assertEquals(299, count($data->locations), "Language: {$lang}");
            $this->assertEquals(0, count($data->parameters), "Language: {$lang}");

            $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
            $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
        }
    }

    public function testParseMetadataVQHA80()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validMetadata/VQHA80_de.txt');
        $data = \cstuder\ParseSwissMetNet\MetadataParser::parse($raw);

        $this->assertEquals(159, count($data->locations));
        $this->assertEquals(20, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }

    public function testParseMetadataVQHA98()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validMetadata/VQHA98_it.txt');
        $data = \cstuder\ParseSwissMetNet\MetadataParser::parse($raw);

        $this->assertEquals(132, count($data->locations));
        $this->assertEquals(1, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }

    public function testParseLegacyMetadata()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validMetadata/VQHA69_EN.txt');
        $data = \cstuder\ParseSwissMetNet\MetadataParser::parse($raw);

        $this->assertEquals(114, count($data->locations));
        $this->assertEquals(10, count($data->parameters));

        $this->assertContainsOnlyInstancesOf('StdClass', $data->locations);
        $this->assertContainsOnlyInstancesOf('StdClass', $data->parameters);
    }
}
