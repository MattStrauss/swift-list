<?php

namespace Tests\Feature\Authenticated;

use App\Aisle;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AisleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function userCanAccessAislesIndex()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/aisles');

        $response->assertOk();
        $response->assertViewHas('aisles');
    }

    /** @test */
    public function userCanChangeTheirDefaultAisleOrder()
    {
        $user = factory(User::class)->create();
        $aisles = factory(Aisle::class, 4)->create();
        $aislesReversed = $aisles->reverse()->toArray();

        $response = $this->actingAs($user)->post('/aisles', $aislesReversed);

        $response->assertOk();
        $this->assertDatabaseHas('users',
            ['id' => $user->id, 'aisle_order' => '[4,3,2,1]']);
    }
}
