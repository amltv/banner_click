<?php

namespace Tests\Feature;

use App\Models\Banner;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testClick()
    {
        $banner = factory(Banner::class)->make();

        $response = $this->get("/banner/click/{$banner->id}");
        $this->assertEquals(1, 1);


        //Redis::get
        //Тут можно делать тест =)
    }
}
