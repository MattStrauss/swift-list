<?php

namespace Tests\Unit;

use App\Aisle;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AisleTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   public function itReturnAislesInCustomOrder()
   {
       factory(Aisle::class, 4)->create();
       $user = factory(User::class)->create(['aisle_order' => [3,1,4,2]]);

       $aislesInCustomOrder = Aisle::withCustomOrder($user)->pluck('id')->toArray();

       $this->assertEquals([3,1,4,2], $aislesInCustomOrder);
   }
}
