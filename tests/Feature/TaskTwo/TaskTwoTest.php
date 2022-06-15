<?php

namespace Tests\Feature;

use App\Http\Livewire\CreateOrder;
use App\Listeners\MergeTheCart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RefactorWeekThreeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_saves_the_cart_on_db_and_returns_if_you_login_recuperacion()
    {
        $data = $this->generate_product_refactor(2, true);

        $cartOriginalContent = $data['cart'];

        $this->post('/logout');

        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($cartOriginalContent)]);

        $cartLogin = new MergeTheCart();

        $login = new Login('login', $data['users'][0], true);
        $this->actingAs($data['users'][0]);

        $data2 = $this->generate_product_refactor(2);

        $cartLogin->handle($login);

        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($cartOriginalContent)]);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][1]->name)
            ->assertSee($data['products'][0]->quantity)
            ->assertSee($data['products'][1]->quantity)
            ->assertSee($data['products'][0]->price)
            ->assertSee($data['products'][1]->price)
            ->assertDontSee($data2['products'][0]->name)
            ->assertDontSee($data2['products'][1]->name);
    }
}
