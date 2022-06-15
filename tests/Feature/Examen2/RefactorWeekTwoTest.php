<?php

namespace Tests\Feature\Examen2;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RefactorWeekTwoTest extends TestCase
{
    use RefreshDatabase;

    // ************* TAREA 1 *******************
    /** @test */
    public function it_shows_login_and_register_links_if_you_are_logged()
    {
        $this->generate_product_refactor();

        $this->get('/')
            ->assertSee('Perfil')
            ->assertSee('Finalizar sesión')
            ->assertDontSee('Iniciar sesión')
            ->assertDontSee('Registrarse');
   }

    /** @test */
    public function it_not_shows_login_and_register_links_if_you_are_logged()
    {
        $this->generate_product_refactor(1,false,false,false,0,false,10,2,1, true,
            0);

        $this->get('/')
            ->assertSee('Iniciar sesión')
            ->assertSee('Registrarse')
            ->assertDontSee('Perfil')
            ->assertDontSee('Finalizar sesión');
    }

    // ************* TAREA 2 *******************
//    /** @test */
//    public function it_shows_five_products_of_one_category()
//    {
//        $data = $this->generate_product_refactor(5,false,false,false,0,false,10,2,1,
//            true);
//
//        dump($data['category']->name, $data['products'][0]->name);
//
//        $this->get('/')
//            ->assertSee($data['category']->name)
//            ->assertSee($data['anotherCategory']->name)
//            ->assertSee('Ver más')
//            ->assertSee($data['products'][0]->name);
//
//        $this->assertEquals(5, Product::where('subcategory_id', $data['subcategory']->id)->count());
//        $this->assertEquals(6, Product::all()->count());
//    }*/

    // ************* TAREA 4 *******************
    /** @test */
    public function it_shows_categories_details()
    {
        $data = $this->generate_product_refactor(2,false,false,false,0,false,10,2,1,
            true);

        $this->get('/categories/' . $data['category']->slug)
            ->assertSee($data['subcategory']->name)
            ->assertSee($data['brand']->name)
            ->assertSee(Str::limit($data['products'][0]->name, 20))
            ->assertSee(Str::limit($data['products'][1]->name, 20))
            ->assertDontSee(Str::limit($data['anotherProduct']->name, 20));

        $this->assertEquals(1, Product::where('subcategory_id', $data['anotherSubcategory']->id)->count());
        $this->assertEquals(2, Product::where('subcategory_id', $data['subcategory']->id)->count());
        $this->assertEquals(3, Product::all()->count());
    }

    // ************* TAREA 6 y TAREA 9 *******************
    /** @test */
    public function it_shows_product_details()
    {
        $data = $this->generate_product_refactor();

        $this->get('/products/' . $data['products'][0]->slug)
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][0]->price)
            ->assertSee($data['products'][0]->description)
            ->assertSee($data['products'][0]->quantity)
            ->assertSee('+')
            ->assertSee('-')
            ->assertDontSee('Color:')
            ->assertDontSee('Talla:');
    }

    /** @test */
    public function it_shows_color_product_details()
    {
        $data = $this->generate_product_refactor(1, false, false, true, 2);

        $this->get('/products/' . Product::first()->slug)
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][0]->price)
            ->assertSee($data['products'][0]->description)
            ->assertSee($data['products'][0]->quantity)
            ->assertSee('+')
            ->assertSee('-')
            ->assertSee('Color:')
            ->assertDontSee('Talla:');
    }

    /** @test */
    public function it_shows_size_product_details()
    {
        $data = $this->generate_product_refactor(1, false, false, true, 2, true);

        $this->get('/products/' . Product::first()->slug)
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][0]->price)
            ->assertSee($data['products'][0]->description)
            ->assertSee($data['products'][0]->quantity)
            ->assertSee('+')
            ->assertSee('-')
            ->assertSee('Color:')
            ->assertSee('Talla:');
    }
}
