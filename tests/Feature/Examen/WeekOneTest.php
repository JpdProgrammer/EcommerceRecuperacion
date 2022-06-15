<?php

namespace Tests\Feature\Examen;

use App\Http\Livewire\CreateOrder;
use App\Listeners\MergeTheCart;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class WeekOneTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_categories_when_yo_u_click()
    {

   }
}
