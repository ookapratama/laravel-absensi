<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserPostTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_post_web_route()
    {
        $response = $this->post('/user', [
            'name' => 'Ooka Pratama',
            'email' => 'ooka2@gmail.com',
            'password' => '123456',
        ]);
        dump(
            config('database.default'),
            DB::connection()->getDatabaseName()
        );

        $response->dump();
        $response->assertStatus(200);
    }
}
