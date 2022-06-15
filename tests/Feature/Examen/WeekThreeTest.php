<?php

namespace Tests\Feature\Examen;

use App\Http\Livewire\CreateOrder;
use App\Listeners\MergeTheCart;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WeekThreeTest extends TestCase
{
    use RefreshDatabase;

    // ************* TAREA 1 *******************
    /** @test */
    public function it_add_product_to_the_cart()
    {
        $this->generate_product_new(1, true);

        $this->get('/shopping-cart')
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertDontSee('Color:');
   }

    /** @test */
    public function it_add_color_product_to_the_cart()
    {
        $this->generate_product_new(1, true, false, true);

        $this->get('/shopping-cart')
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee('Color:');
    }

    /** @test */
    public function it_add_size_product_to_the_cart()
    {
        $this->generate_product_new(1, true, false, true, 12, true);

        $this->get('/shopping-cart')
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee('Color:')
            ->assertSee(Size::first()->name);
    }

    // ************* TAREA 5 *******************
    /** @test */
    public function it_shows_product_stock()
    {
        $this->generate_product_new();

        $this->get('/products/' . Product::first()->slug)
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee(Product::first()->quantity)
            ->assertDontSee('Color:')
            ->assertDontSee('Talla:');

        $this->assertEquals(10, qty_available(Product::first()->id));
    }

    /** @test */
    public function it_shows_color_product_stock()
    {
        $this->generate_product_new(1, false, false, true, 2);

        $this->get('/products/' . Product::first()->slug)
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee(Product::first()->quantity)
            ->assertSee('Color:')
            ->assertDontSee('Talla:');

        $this->assertEquals(2, qty_available(Product::first()->id, 1));
    }

    /** @test */
    public function it_shows_size_product_stock()
    {
        $this->generate_product_new(1, false, false, true, 2, true);

        $this->get('/products/' . Product::first()->slug)
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee(Product::first()->quantity)
            ->assertSee('Color:')
            ->assertSee('Talla:');

        $this->assertEquals(2, qty_available(Product::first()->id, 1, 1));
    }

    // ************* TAREA 6 *******************
    /** @test */
    public function it_products_when_filter_search()
    {
        $this->generate_product_to_search('alex');
        $this->generate_product_to_search('aaalexbbb');

        $this->get('/search?name=' . Product::first()->name)
            ->assertSee(Product::first()->name)
            ->assertSee(Product::find(3)->name)
            ->assertDontSee(Product::find(2)->name)
            ->assertDontSee(Product::find(4)->name);
    }

    /** @test */
    public function it_show_all_products_when_empty_search()
    {
        $this->generate_product_to_search('alex');
        $this->generate_product_to_search('aaalexbbb');

        $this->get('/search')
            ->assertSee(Product::first()->name)
            ->assertSee(Product::find(3)->name)
            ->assertSee(Product::find(2)->name)
            ->assertSee(Product::find(4)->name);
    }

    // ************* TAREA 7 *******************
    /** @test */
        public function it_shows_all_products_added_to_the_cart()
    {
        $this->generate_product_new(2,true,false,false,0,false,10,2,1,
            true);

        $this->get('/shopping-cart')
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee(Product::find(2)->name)
            ->assertSee(Product::find(2)->price)
            ->assertDontSee(Product::find(3)->name)
            ->assertDontSee(Product::find(3)->price);
    }

    // ************* TAREA 8 *******************
    /** @test */
    public function it_can_change_the_stock_on_shopping_cart()
    {
        $this->generate_product_new(1, true);

        foreach (Cart::content() as $item) {
            $rowId = $item->rowId;
            $qty = $item->qty;
        }

        Livewire::test('update-cart-item', ['rowId' => $rowId, 'qty' => $qty])
            ->call('increment');

        $this->get('/shopping-cart')
            ->assertSee('Total')
            ->assertSee(Product::first()->price * 2);
    }

    // ************* TAREA 9 *******************
    /** @test */
    public function it_can_delete_the_cart()
    {
        $this->generate_product_new(1, true);

        $this->assertEquals(1, Cart::content()->count());

        Livewire::test('shopping-cart')
            ->call('destroy');

        $this->get('/shopping-cart')
        ->assertDontSee(Product::first()->name);

        $this->assertEquals(0, Cart::content()->count());
    }

    /** @test */
    public function it_can_delete_one_product_of_the_cart()
    {
        $this->generate_product_new(3, true);

        $rowId = [];

        foreach (Cart::content() as $item) {
            $rowId[] = $item->rowId;
        }

        Livewire::test('shopping-cart')
            ->call('delete', $rowId[1]);

        $this->get('/shopping-cart')
            ->assertSee(Product::first()->name)
            ->assertDontSee(Product::find(2)->name)
            ->assertSee(Product::find(3)->name);
    }

    // ************* TAREA 10 *******************
    /** @test */
    public function it_can_create_an_order_if_its_logged()
    {
        $this->generate_product_new(2,true);

        $this->get('orders/create')
            ->assertStatus(200)
            ->assertSee('Envíos');
    }

    /** @test */
    public function it_cant_create_an_order_if_its_logged()
    {
        $this->generate_product_new(2,true,false,false,0,false,10,2,1, false,
            0);

        $this->get('orders/create')
            ->assertDontSee('Envíos')
            ->assertRedirect('/login');
    }

    // ************* TAREA 11 *******************
    /** @test */
    public function it_saves_the_cart_on_db_and_returns_if_you_login()
    {
        $this->generate_product_new(2, true);

        $cartContent = Cart::content();

        $this->post('/logout');

        //$this->assertDatabaseHas('shoppingcart', ['content' => serialize($cartContent)]);

        $cartLogin = new MergeTheCart();

        $login = new Login('login', User::first(), true);
        $this->actingAs(User::first());

        $this->generate_product_new(2);

        $cartLogin->handle($login);

        $this->get('/shopping-cart')
            ->assertSee(Product::first()->name)
            ->assertSee(Product::find(2)->name)
            ->assertDontSee(Product::find(3)->name)
            ->assertDontSee(Product::find(4)->name);
    }

    // ************* TAREA 12 *******************
    /** @test */
    public function it_shows_create_order()
    {
        $this->generate_product_new(2, true);

        Livewire::test(CreateOrder::class, ['envio_type' => 1])
            ->set('contact', 'Contacto')
            ->set('phone', '123456789')
            ->set('shipping_cost', 1)
            ->set('department_id', 1);
    }

    // ************* TAREA 13 *******************
    /** @test */
    public function it_destroy_the_cart_when_order_is_complete()
    {
        $this->generate_product_new(2, true);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'Contacto')
            ->set('phone', '123456789')
            ->set('envio_type', 1)
            ->set('shipping_cost', 1)
            ->call('create_order')
            ->assertRedirect('/orders/1/payment');

        $this->assertEquals(0, Cart::content()->count());
        $this->assertDatabaseCount('orders', 1);
    }
}
