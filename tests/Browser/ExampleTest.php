<?php

namespace Tests\Browser;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use  DatabaseMigrations;

    /** @test */
    public function testBasicExample()
    {
        Category::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Hola')
                ->screenshot('TestBasdsfsdicExample');
        });
    }
}
