<?php

namespace Tests\Feature\Examen;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WeekTwoTest extends TestCase
{
    use RefreshDatabase;

    // ************* TAREA 1 *******************
    /** @test */
    public function it_shows_login_and_register_links_if_you_are_logged()
    {
        $this->generate_product_new();

        $this->get('/')
            ->assertSee('Perfil')
            ->assertSee('Finalizar sesión')
            ->assertDontSee('Iniciar sesión')
            ->assertDontSee('Registrarse');
   }

    /** @test */
    public function it_not_shows_login_and_register_links_if_you_are_logged()
    {
        $this->generate_product_new(1,false,false,false,0,false,10,2,1, true,
            0);

        $this->get('/')
            ->assertSee('Iniciar sesión')
            ->assertSee('Registrarse')
            ->assertDontSee('Perfil')
            ->assertDontSee('Finalizar sesión');
    }

    // ************* TAREA 2 *******************
    /** @test */
    public function it_shows_five_products_of_one_category()
    {
        $this->get('/')
            ->assertSee(Category::first()->name)
            ->assertSee('Ver más')
            ->assertSee(Product::first()->name);

        $this->assertEquals(6, Product::where('subcategory_id', 1)->count());
        $this->assertEquals(6, Product::all()->count());
    }

    // ************* TAREA 4 *******************
    /** @test */
    public function it_shows_categories_details()
    {
        $this->generate_product_new(2,false,false,false,0,false,10,2,1,
            true);

        $this->get('/categories/' . Category::first()->slug)
            ->assertSee(Subcategory::first()->name)
            ->assertSee(Brand::first()->name)
            ->assertSee(Str::limit(Product::first()->name, 20))
            ->assertSee(Str::limit(Product::find(2)->name, 20))
            ->assertDontSee(Str::limit(Product::find(3)->name, 20));

        $this->assertEquals(1, Product::where('subcategory_id', 2)->count());
        $this->assertEquals(2, Product::where('subcategory_id', 1)->count());
        $this->assertEquals(3, Product::all()->count());
    }

    // ************* TAREA 6 y TAREA 9 *******************
    /** @test */
    public function it_shows_product_details()
    {
        $this->generate_product_new();

        $this->get('/products/' . Product::first()->slug)
            ->assertSee(Str::limit(Product::first()->name, 20))
            ->assertSee(Product::first()->price)
            ->assertSee(Product::first()->description)
            ->assertSee(Product::first()->quantity)
            ->assertSee('+')
            ->assertSee('-')
            ->assertDontSee('Color:')
            ->assertDontSee('Talla:');
    }

    /** @test */
    public function it_shows_color_product_details()
    {
        $this->generate_product_new(1, false, false, true, 2);

        $this->get('/products/' . Product::first()->slug)
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee(Product::first()->description)
            ->assertSee(Product::first()->quantity)
            ->assertSee('+')
            ->assertSee('-')
            ->assertSee('Color:')
            ->assertDontSee('Talla:');
    }

    /** @test */
    public function it_shows_size_product_details()
    {
        $this->generate_product_new(1, false, false, true, 2, true);

        $this->get('/products/' . Product::first()->slug)
            ->assertSee(Product::first()->name)
            ->assertSee(Product::first()->price)
            ->assertSee(Product::first()->description)
            ->assertSee(Product::first()->quantity)
            ->assertSee('+')
            ->assertSee('-')
            ->assertSee('Color:')
            ->assertSee('Talla:');
    }
}
