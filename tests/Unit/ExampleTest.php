<?php

namespace Tests\Unit;

use App\Components\BannerComponent;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    public function testBuildKeyString()
    {
        $class = new \ReflectionClass(BannerComponent::class);
        $method = $class->getMethod('buildKeyString');
        $method->setAccessible(true);

        $bannerComponent = $this->app->make('banner');

        $this->assertEquals(
            $method->invokeArgs($bannerComponent, ['test_1', 1, strtotime(date('2018-02-28 10:01:00')), 2]),
            $method->invokeArgs($bannerComponent, ['test_1', 1, strtotime(date('2018-02-28 10:55:00')), 2])
        );

        $this->assertNotEquals(
            $method->invokeArgs($bannerComponent, ['test_1', 1, strtotime(date('2018-02-28 10:01:00')), 2]),
            $method->invokeArgs($bannerComponent, ['test_1', 1, strtotime(date('2018-02-28 11:55:00')), 2])
        );
    }
}
