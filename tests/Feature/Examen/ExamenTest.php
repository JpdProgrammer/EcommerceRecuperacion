<?php
//
//namespace Tests\Feature\Examen;
//
//use App\Http\Livewire\CreateOrder;
//use App\Listeners\MergeTheCart;
//use App\Models\Brand;
//use App\Models\Category;
//use App\Models\Product;
//use App\Models\Subcategory;
//use App\Models\User;
//use Gloudemans\Shoppingcart\Facades\Cart;
//use Illuminate\Auth\Events\Login;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
//use Illuminate\Support\Facades\DB;
//use Livewire\Livewire;
//use Tests\TestCase;
//
//class ExamenTest extends TestCase
//{
//    use RefreshDatabase;
//
//    /** @test */
//    public function it_show_stock_available_of_a_normal_product()
//    {
//        $this->generate_product();
//
//        $this->get('/products/' . Product::first()->slug)
//            ->assertSee(Product::first()->name)
//            ->assertSee('Stock disponible:')
//            ->assertDontSee('Color:')
//            ->assertDontSee('Talla:')
//            ->assertSee(Product::first()->quantity);
//    }
//
//    /** @test */
//    public function it_show_stock_available_of_a_product_with_color()
//    {
//        $this->generate_product(1, true, false, true, 1);
//
//        $this->get('/products/' . Product::first()->slug)
//            ->assertSee(Product::first()->name)
//            ->assertSee('Color:')
//            ->assertDontSee('Talla:')
//            ->assertSee('Stock disponible:')
//            ->assertSee(Product::first()->quantity);
//    }
//
//    /** @test */
//    public function it_show_stock_available_of_a_product_with_size()
//    {
//        $this->generate_product(1, true, false, true, 1, true);
//
//        $this->get('/products/' . Product::first()->slug)
//            ->assertSee(Product::first()->name)
//            ->assertSee('Color:')
//            ->assertSee('Talla:')
//            ->assertSee('Stock disponible:')
//            ->assertSee(Product::first()->quantity);
//    }
//
//    /** @test */
//    public function it_add_a_product_to_the_cart()
//    {
//        $this->generate_product(2, true);
//
//        $this->get('/shopping-cart')
//            ->assertSee(Product::first()->name)
//            ->assertDontSee(Product::find(2)->name);
//    }
//
//    /** @test */
//    public function it_shows_five_products_published_of_a_category()
//    {
//        $this->generate_product(5);
//
//        $this->get('/categories/' . Category::first()->slug)
//            ->assertSee(Product::first()->name)
//            ->assertSee(Product::find(2)->name)
//            ->assertSee(Product::find(3)->name)
//            ->assertSee(Product::find(4)->name)
//            ->assertSee(Product::find(5)->name);
//    }
//
//    /** @test */
//    public function it_shows_category_details()
//    {
//        $this->generate_product();
//
//        $this->get('/categories/' . Category::first()->slug)
//            ->assertSee(Subcategory::first()->name)
//            ->assertSee(Brand::first()->name)
//            ->assertSee(Product::first()->name);
//    }
//
//    /** @test */
//    public function it_shows_products_details()
//    {
//        $this->generate_product();
//
//        $this->get('/products/' . Product::first()->slug)
//            ->assertSee(Product::first()->name)
//            ->assertSee(Product::first()->price)
//            ->assertSee(Product::first()->description)
//            ->assertSee('Stock disponible:')
//            ->assertSee(Product::first()->quantity)
//            ->assertSee('Agregar al carrito de compras')
//            ->assertSee('+')
//            ->assertSee('-');
//    }
//
//    /** @test */
//    public function it_destroys_the_cart_when_creates_a_order()
//    {
//        $this->generate_product(1, true, true);
//
//        Livewire::test(CreateOrder::class)
//            ->set('contact', 'Contacto')
//            ->set('phone', '123456789')
//            ->set('envio_type', 1)
//            ->set('shipping_cost', 1)
//            ->call('create_order')
//            ->assertRedirect('/orders/1/payment');
//
//        $this->get('/shopping-cart')
//            ->assertDontSee(Product::first()->name);
//
//        $this->assertDatabaseCount('orders', 1);
//
//    }
//
//    /** @test */
//    public function the_search_filters()
//    {
//        $this->generate_search('soyalejandro');
//
//        $this->get('/search?name=alejandro')
//                ->assertSee(Product::find(1)->name)
//                ->assertDontSee(Product::find(2)->name);
//
//    }
//
//    /** @test */
//    public function the_search_show_all_products_if_its_empty()
//    {
//        $this->generate_search();
//
//        $this->get('/search?name=')
//            ->assertSee(Product::find(1)->name)
//            ->assertSee(Product::find(2)->name);
//
//    }
//
//    /** @test */
//    public function it_saves_products_on_cart_when_logout() {
//
//        $this->generate_product(1, true);
//        $this->generate_product();
//
//        $producto1 = Product::first();
//        $producto2 = Product::find(2);
//        $this->createCart($producto2, 1);
//
//        $user = User::factory()->create();
//        $this->actingAs($user);
//
//        $cartContent = Cart::content();
//
//        $this->post('/logout');
//
//        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($cartContent)]);
//
//        $cartLogin = new MergeTheCart();
//
//        $login = new Login('login', $user, true);
//        $this->actingAs($user);
//
//        $cartLogin->handle($login);
//
//        $this->get('/shopping-cart')
//            ->assertSee($producto1->name)
//            ->assertSee($producto2->name)
//            ->assertSee($producto1->quantity)
//            ->assertSee($producto2->quantity)
//            ->assertSee($producto1->price)
//            ->assertSee($producto2->price);
//    }
//
//    /** @test */
//    public function it_can_acces_if_its_logged()
//    {
//        $this->generate_product();
//
//        $producto1 = Product::find(1);
//        $this->createCart($producto1, 1);
//
//        $user = User::factory()->create();
//        $this->actingAs($user);
//
//        $this->get('orders/create')
//            ->assertSee('Envíos');
//    }
//
//    /** @test */
//    public function it_cant_acces_if_its_not_logged()
//    {
//        $this->generate_product();
//
//        $producto1 = Product::find(1);
//        $this->createCart($producto1, 1);
//
//        $this->get('orders/create')
//            ->assertDontSee('Envíos')
//            ->assertRedirect('/login');
//    }
//
//    /** @test */
//    public function it_access_to_order_if_you_are_the_correct_user()
//    {
//        $this->generate_product(1, true, true);
//
//        $producto1 = Product::first();
//        $user1 = User::find(1);
//
//        $this->actingAs($user1);
//
//        $this->get('orders/1/payment')
//            ->assertStatus(200)
//            ->assertSee('Resumen')
//            ->assertDontSee('Hola buenas tardes')
//            ->assertSee($producto1->name);
//    }
//
//    /** @test */
//    public function it_dont_access_to_order_if_you_are_the_correct_user()
//    {
//        $this->generate_product(1, true, true);
//
//        $producto1 = Product::first();
//
//        $user2 = User::factory()->create();
//
//        $this->actingAs($user2);
//
//        $this->get('orders/1/payment')
//            ->assertSee('Hola buenas tardes')
//            ->assertDontSee('Resumen')
//            ->assertDontSee($producto1->name);
//    }
//
//}
