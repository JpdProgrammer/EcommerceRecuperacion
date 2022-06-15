<?php

namespace Tests\Feature\Examen2;

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

    // ************* TAREA 1 *******************
    /** @test */
    public function it_add_product_to_the_cart()
    {
        $data = $this->generate_product_refactor(1, true);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertDontSee('Color:');
   }

    /** @test */
    public function it_add_color_product_to_the_cart()
    {
        $data = $this->generate_product_refactor(1, true, false, true);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertSee('Color:');
    }

    /** @test */
    public function it_add_size_product_to_the_cart()
    {
        $data = $this->generate_product_refactor(1, true, false, true, 12, true);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertSee('Color:')
            ->assertSee($data['size']->name);
    }

    // ************* TAREA 5 *******************
    /** @test */
    public function it_shows_product_stock()
    {
        $data = $this->generate_product_refactor();

        $this->get('/products/' . $data['products'][0]->slug)
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][0]->quantity)
            ->assertDontSee('Color:')
            ->assertDontSee('Talla:');

        $this->assertEquals(10, qty_available($data['products'][0]->id));
    }

    /** @test */
    public function it_shows_color_product_stock()
    {
        $data = $this->generate_product_refactor(1, false, false, true, 2);

        $this->get('/products/' . $data['products'][0]->slug)
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][0]->quantity)
            ->assertSee('Color:')
            ->assertDontSee('Talla:');

        $this->assertEquals(2, qty_available($data['products'][0]->id, $data['color']->id));
    }

    /** @test */
    public function it_shows_size_product_stock()
    {
        $data = $this->generate_product_refactor(1, false, false, true, 2, true);

        $this->get('/products/' . $data['products'][0]->slug)
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][0]->quantity)
            ->assertSee('Color:')
            ->assertSee('Talla:');

        $this->assertEquals(2, qty_available($data['products'][0]->id, $data['color']->id, $data['size']->id));
    }

    // ************* TAREA 6 *******************
    /** @test */
    public function it_products_when_filter_search()
    {
        $data = $this->generate_product_to_search_refactor('alex');

         $this->get('/search?name=alex')
            ->assertSee($data['productSearched']->name)
            ->assertDontSee($data['product']->name);
    }

    /** @test */
    public function it_show_all_products_when_empty_search()
    {
        $data = $this->generate_product_to_search_refactor('alex');

        $this->get('/search')
            ->assertSee($data['productSearched']->name)
            ->assertSee($data['product']->name);
    }

    // ************* TAREA 7 *******************
    /** @test */
        public function it_shows_all_products_added_to_the_cart()
    {
        $data = $this->generate_product_refactor(2,true,false,false,0,false,10,2,1,
            true);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][1]->name)
            ->assertDontSee($data['anotherProduct']->name);
    }

    // ************* TAREA 8 *******************
    /** @test */
    public function it_can_change_the_stock_on_shopping_cart()
    {
        $data = $this->generate_product_refactor(1, true);

        foreach ($data['cart'] as $item) {
            $rowId = $item->rowId;
            $qty = $item->qty;
        }

        Livewire::test('update-cart-item', ['rowId' => $rowId, 'qty' => $qty])
            ->call('increment');

        $this->get('/shopping-cart')
            ->assertSee('Total')
            ->assertSee($data['products'][0]->price * 2);
    }

    // ************* TAREA 9 *******************
    /** @test */
    public function it_can_delete_the_cart()
    {
        $data = $this->generate_product_refactor(1, true);

        $this->assertEquals(1, $data['cart']->count());

        Livewire::test('shopping-cart')
            ->call('destroy');

        $this->get('/shopping-cart')
        ->assertDontSee($data['products'][0]->name);

        $this->assertEquals(0, Cart::content()->count());
    }

    /** @test */
    public function it_can_delete_one_product_of_the_cart()
    {
        $data = $this->generate_product_refactor(3, true);

        $rowId = [];

        foreach (Cart::content() as $item) {
            $rowId[] = $item->rowId;
        }

        Livewire::test('shopping-cart')
            ->call('delete', $rowId[1]);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['products'][1]->name)
            ->assertSee($data['products'][2]->name);
    }

    // ************* TAREA 10 *******************
    /** @test */
    public function it_can_create_an_order_if_its_logged()
    {
        $this->generate_product_refactor(2,true);

        $this->get('orders/create')
            ->assertStatus(200)
            ->assertSee('Envíos');
    }

    /** @test */
    public function it_cant_create_an_order_if_its_logged()
    {
        $this->generate_product_refactor(2,true,false,false,0,false,10,2,1, false,
            0);

        $this->get('orders/create')
            ->assertDontSee('Envíos')
            ->assertRedirect('/login');
    }

    // ************* TAREA 11 *******************
    /** @test */
    public function it_saves_the_cart_on_db_and_returns_if_you_login()
    {
        $data = $this->generate_product_refactor(2, true);

        $cartContent = $data['cart'];

        $this->post('/logout');

        //$this->assertDatabaseHas('shoppingcart', ['content' => serialize($cartContent)]);

        $cartLogin = new MergeTheCart();

        $login = new Login('login', $data['users'][0], true);
        $this->actingAs($data['users'][0]);

        $data2 = $this->generate_product_refactor(2);

        $cartLogin->handle($login);

        $this->get('/shopping-cart')
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][1]->name)
            ->assertDontSee($data2['products'][0]->name)
            ->assertDontSee($data2['products'][1]->name);
    }

    // ************* TAREA 12 *******************
    /** @test */
    public function it_shows_create_order()
    {
        $this->generate_product_refactor(2, true);

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
        $data = $this->generate_product_refactor(2, true);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'Contacto')
            ->set('phone', '123456789')
            ->set('envio_type', 1)
            ->set('shipping_cost', 1)
            ->call('create_order')
            ->assertRedirect('/orders/' . Order::first()->id . '/payment');

        $this->assertEquals(0, Cart::content()->count());
        $this->assertDatabaseCount('orders', 1);
    }
}
