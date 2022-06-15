<?php

namespace Tests\Feature\Examen;

use App\Models\Order;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeekFourTest extends TestCase
{
    use RefreshDatabase;

    // ************* TAREA 1 *******************
    /** @test */
    public function it_can_acces_if_its_logged()
    {
        $this->generate_product_new(1, true);

        $this->get('orders/create')
            ->assertSee('Envíos');
    }

    /** @test */
    public function it_cant_acces_if_its_not_logged()
    {
        $this->generate_product_new(1,true,false,false,0,false,10,2,1, false,
        0);

        $this->get('orders/create')
            ->assertDontSee('Envíos')
            ->assertRedirect('/login');
    }

    // ************* TAREA 2 *******************
    /** @test */
    public function it_access_to_order_if_you_are_the_correct_user()
    {
        $this->generate_product_new(1, true, true);

        $this->get('orders/1/payment')
            ->assertSee('Resumen')
            ->assertDontSee('Hola buenas tardes')
            ->assertSee(Product::first()->name);
    }

    /** @test */
    public function it_dont_access_to_order_if_you_are_the_correct_user()
    {
        $this->generate_product_new(1,true,true,false,0,false,10,2,1, false,
            2, true, 1);

        $this->generate_product_new(1,true,true,false,0,false,10,2,1, false,
            1, true, 2);

        $this->get('orders/1/payment')
            ->assertSee('Hola buenas tardes')
            ->assertDontSee('Resumen')
            ->assertDontSee(Product::first()->name);
    }

    // ************* TAREA 3 *******************
    /** @test */
    public function it_redirect_correctly()
    {
        $this->generate_product_new(2, true, true);

        $this->get('/orders')
            ->assertSee('Pendiente')
            ->assertSee('Recibido')
            ->assertSee('Enviado')
            ->assertSee('Entregado')
            ->assertSee('Anulado')
            ->assertSee('Pedidos recientes')
            ->assertSee(Order::first()->total)
            ->assertDontSee('No existen registros de pedidos');

        $this->assertEquals(1, Order::all()->count());
    }

    // ************* TAREA 4 *******************
    /** @test */
    public function the_stock_changes_when_add_a_product_to_the_cart()
    {
        $this->generate_product_new(1, true, false, false, 0, false, 546);

        $this->get('products/' . Product::first()->slug)
            ->assertSee('Stock disponible:')
            ->assertSee(Product::first()->quantity - 1);

        $this->assertEquals(545, qty_available(Product::first()->id));
    }

    /** @test */
    public function the_stock_changes_when_add_a_color_product_to_the_cart()
    {
        $this->generate_product_new(1, true, false, true, 12);

        $this->get('products/' . Product::first()->slug)
            ->assertSee('Stock disponible:')
            ->assertSee(Product::first()->colors->find(1)->pivot->quantity - 1);

        $this->assertEquals(11, qty_available(Product::first()->id, 1));
    }

    /** @test */
    public function the_stock_changes_when_add_a_size_product_to_the_cart()
    {
        $this->generate_product_new(4, true, false, true, 12, true);

        $this->get('products/' . Product::first()->slug)
            ->assertSee('Stock disponible:')
            ->assertSee(Product::first()->colors->find(1)->pivot->quantity - 1);

        $this->assertEquals(11, qty_available(Product::first()->id, 1, 1));
    }

    /** @test */
    public function it_changes_the_stock_when_generate_a_order()
    {
        $this->generate_product_new(1, true, true, false, 0, false, 546);

        $this->get('products/' . Product::first()->slug)
            ->assertSee('Stock disponible:')
            ->assertSee('545');

        $this->assertEquals(1, Order::all()->count());
        $this->assertEquals(0, Cart::content()->count());
        $this->assertEquals(545, qty_available(Product::first()->id));
    }
}
