<?php

use PHPUnit\Framework\TestCase;

class DataParserTest extends TestCase
{
    public function testParseDataVQHA80()
    {
        $raw = file_get_contents(__DIR__ . '/resources/validData/VQHA80.csv');
        $data = \cstuder\ParseSwissMetNet\DataParser::parse($raw);

        $this->assertEquals(1740, count($data));
        $this->assertEquals(20, count($this->collectParameters($data)));
        $this->assertEquals(159, count($this->collectLocations($data)));
        $this->assertEquals(1, count($this->collectTimestamps($data)));
    }



    private function collectParameters($data)
    {
        $allParameters = array_map(function ($d) {
            return $d->par;
        }, $data);

        return array_unique($allParameters);
    }

    private function collectLocations($data)
    {
        $allLocations = array_map(function ($d) {
            return $d->loc;
        }, $data);

        return array_unique($allLocations);
    }

    private function collectTimestamps($data)
    {
        $allTimestamps = array_map(function ($d) {
            return $d->timestamp;
        }, $data);

        return array_unique($allTimestamps);
    }
}
