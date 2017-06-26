<?php

namespace RocketScience;

use PHPUnit\Framework\TestCase;

/** @group unit */
class LaunchLogProcessorTest extends TestCase
{
    /** @var LaunchLogProcessor */
    private $processor;

    /** @test */
    public function it_returns_an_array()
    {
        $stream = fopen('php://temp', 'r');

        $this->assertInternalType('array', $this->processor->groupBy($stream, null, null));

        fclose($stream);
    }

    /** @test */
    public function it_throw_exception_when_not_stream_given()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->assertInternalType('array', $this->processor->groupBy('launch.log', null, null));
    }

    public function it_validates_group_by_parameters()
    {
    }
    public function it_validates_filter_by_successful_parameter()
    {
    }

    /** @test */
    public function it_counts_launches_by_year()
    {
        $stream = fopen(__DIR__.'/launch-1.log', 'r');

        $result = $this->processor->groupBy($stream, 'year', null);

        $this->assertEquals(
            [
                '1957' => 4,
                '1958' => 7,
                '1959' => 11,
            ],
            $result
        );
    }

    /** @test */
    public function it_counts_launches_by_year_and_omits_multi_payload_launch()
    {
        $stream = fopen(__DIR__.'/launch-2.log', 'r');

        $result = $this->processor->groupBy($stream, 'year', null);

        $this->assertEquals(
            [
                '1960' => 40,
            ],
            $result
        );
    }

    /** @test */
    public function it_counts_launches_by_month_and_omits_multi_payload_launch()
    {
        $stream = fopen(__DIR__.'/launch-2.log', 'r');

        $result = $this->processor->groupBy($stream, 'month', null);

        $this->assertEquals(
            [
                'Feb' => 3,
                'Mar' => 2,
                'Apr' => 5,
                'May' => 3,
                'Jun' => 2,
                'Jul' => 1,
                'Aug' => 5,
                'Sep' => 3,
                'Oct' => 6,
                'Nov' => 4,
                'Dec' => 6,
            ],
            $result
        );
    }

    /** @test */
    public function it_counts_launches_only_successful_launches()
    {
        $stream = fopen(__DIR__.'/launch-2.log', 'r');

        $result = $this->processor->groupBy($stream, 'year', true);

        $this->assertEquals(
            [
                '1960' => 21,
            ],
            $result
        );
    }

    /** @test */
    public function it_counts_launches_only_failure_launches()
    {
        $stream = fopen(__DIR__.'/launch-2.log', 'r');

        $result = $this->processor->groupBy($stream, 'year', false);

        $this->assertEquals(
            [
                '1960' => 19,
            ],
            $result
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->processor = new LaunchLogProcessor();
    }

    protected function tearDown()
    {
        $this->processor = null;

        parent::tearDown();
    }
}
