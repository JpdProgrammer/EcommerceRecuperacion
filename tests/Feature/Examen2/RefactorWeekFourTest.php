<?php

namespace Tests\Feature\Examen2;

use App\Http\Livewire\Admin\CreateProduct;
use App\Http\Livewire\Admin\ShowProducts;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RefactorWeekFourTest extends TestCase
{
    use RefreshDatabase;

    // ************* TAREA 1 *******************

    /** @test */
    public function it_can_acces_if_its_logged()
    {
        $this->generate_product_refactor(1, true);

        $this->get('orders/create')
            ->assertSee('Envíos');
    }

    /** @test */
    public function it_cant_acces_if_its_not_logged()
    {
        $this->generate_product_refactor(1, true, false, false, 0, false, 10, 2, 1, false,
            0);

        $this->get('orders/create')
            ->assertDontSee('Envíos')
            ->assertRedirect('/login');
    }

    // ************* TAREA 2 *******************

    /** @test */
    public function it_access_to_order_if_you_are_the_correct_user()
    {
        $data = $this->generate_product_refactor(1, true, true);

        $this->get('orders/1/payment')
            ->assertSee('Resumen')
            ->assertDontSee('Hola buenas tardes')
            ->assertSee($data['products'][0]->name);
    }

    /** @test */
    public function it_dont_access_to_order_if_you_are_the_correct_user()
    {
        $data = $this->generate_product_refactor(1, true, true, false, 0, false, 10, 2, 1, false,
            2, true, 1);

        $this->generate_product_refactor(1, true, true, false, 0, false, 10, 2, 1, false,
            1, true, 2);

        $this->get('orders/' . $data['order']->id . '/payment')
            ->assertSee('Hola buenas tardes')
            ->assertDontSee('Resumen')
            ->assertDontSee($data['products'][0]->name);
    }

    // ************* TAREA 3 *******************
    /** @test */
    public function it_redirect_correctly()
    {
        $data = $this->generate_product_refactor(2, true, true);

        $this->get('/orders')
            ->assertSee('Pendiente')
            ->assertSee('Recibido')
            ->assertSee('Enviado')
            ->assertSee('Entregado')
            ->assertSee('Anulado')
            ->assertSee('Pedidos recientes')
            ->assertSee($data['order']->total)
            ->assertDontSee('No existen registros de pedidos');

        $this->assertEquals(1, Order::all()->count());
    }

    // ************* TAREA 4 *******************

    /** @test */
    public function the_stock_changes_when_add_a_product_to_the_cart()
    {
        $data = $this->generate_product_refactor(1, true, false, false, 0, false, 546);

        $this->get('products/' . $data['products'][0]->slug)
            ->assertSee('Stock disponible:')
            ->assertSee($data['products'][0]->quantity - 1);

        $this->assertEquals(545, qty_available($data['products'][0]->id));
    }

    /** @test */
    public function the_stock_changes_when_add_a_color_product_to_the_cart()
    {
        $data = $this->generate_product_refactor(1, true, false, true, 12);

        $this->get('products/' . $data['products'][0]->slug)
            ->assertSee('Stock disponible:')
            ->assertSee($data['products'][0]->colors->find($data['color']->id)->pivot->quantity - 1);

        $this->assertEquals(11, qty_available($data['products'][0]->id, $data['color']->id));
    }

    /** @test */
    public function the_stock_changes_when_add_a_size_product_to_the_cart()
    {
        $data = $this->generate_product_refactor(4, true, false, true, 12, true);

        $this->get('products/' . $data['products'][0]->slug)
            ->assertSee('Stock disponible:')
            ->assertSee($data['products'][0]->colors->find($data['color']->id)->pivot->quantity - 1);

        $this->assertEquals(11, qty_available($data['products'][0]->id, $data['color']->id, $data['size']->id));
    }

    /** @test */
    public function it_changes_the_stock_when_generate_a_order()
    {
        $data = $this->generate_product_refactor(1, true, true, false, 0, false, 546);

        $this->get('products/' . $data['products'][0]->slug)
            ->assertSee('Stock disponible:')
            ->assertSee('545');

        $this->assertEquals(1, Order::all()->count());
        $this->assertEquals(0, Cart::content()->count());
        $this->assertEquals(545, qty_available($data['products'][0]->id));
    }

    /* ---------------------------------------------------------------------------------

    /** @test */
    public function it_create_validations()
    {
        $data = $this->generate_product_refactor(2);

        Role::create(['name' => 'admin']);
        $userAdmin = User::factory()->create()->assignRole('admin');
        $this->actingAs($userAdmin);

        Livewire::test(ShowProducts::class)
            ->set('search', $data['products'][0]->name)
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['products'][1]->name);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $data = $this->generate_product_refactor();
        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', $data['brand']->id, '32', '23');

        $this->assertDatabaseHas('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_category_id_is_required()
    {
        $data = $this->generate_product_refactor(1,false,false, 1, 12);

        // $this->create_a_new_product('1','1', 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', '1', '32', '23');
        //$this->create_a_new_product('','', '', '', '', '', '', '');
        $this->create_a_new_product('', $data['subcategory']->id, 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', $data['brand']->id, '32', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_subcategory_id_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id,'', 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', $data['brand']->id, '32', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_name_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, '', 'alejandro-garay-lopez', 'holamellamoalejandro', $data['brand']->id, '32', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_slug_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, 'alejandro garay lopez', '', 'holamellamoalejandro', $data['brand']->id, '32', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_description_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, 'alejandro garay lopez', 'alejandro-garay-lopez', '', $data['brand']->id, '32', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_brand_id_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', '', '32', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_price_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', $data['brand']->id, '', '23');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }

    /** @test */
    public function it_dont_create_a_product_because_quantity_is_required()
    {
        $data = $this->generate_product_refactor();

        $this->create_a_new_product($data['category']->id, $data['subcategory']->id, 'alejandro garay lopez', 'alejandro-garay-lopez', 'holamellamoalejandro', $data['brand']->id, '32', '');

        $this->assertDatabaseMissing('products', ['name' => 'alejandro garay lopez']);
    }
}
