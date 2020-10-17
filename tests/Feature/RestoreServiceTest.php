<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestoreServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        //$this->markTestSkipped('RestoreServiceTest skipped');

        $this->registerApp();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->RestoreService->restore();
    }
}
