<?php

namespace Tests\Feature\Examen2;

use App\Http\Livewire\Admin\ShowProducts2;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_show_all_products_when_no_filter()
    {
        $data = $this->generate_product_refactor(3);

        Role::create(['name' => 'admin']);
        $userAdmin = User::factory()->create()->assignRole('admin');
        $this->actingAs($userAdmin);

        $this->get('admin/products2')
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['products'][1]->name)
            ->assertSee($data['products'][2]->name);
    }

    /** @test */
    public function it_filter_when_search_a_product()
    {
        $data = $this->generate_product_refactor(2);

        Livewire::test(ShowProducts2::class)
        ->set('search', $data['products'][0]->name)
        ->assertSee($data['products'][0]->name)
        ->assertDontSee($data['products'][1]->name);
    }

    /** @test */
    public function it_filter_min_price()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 4, 2, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('minPrice', $data['products'][0]->price)
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['anotherProduct']->name);
    }

    /** @test */
    public function it_filter_max_price()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 4, 2, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('maxPrice', $data['anotherProduct']->price)
            ->assertSee($data['anotherProduct']->name)
            ->assertDontSee($data['products'][0]->name);
    }

    /** @test */
    public function it_filter_publish_products()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 4, 1, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('status', 2)
            ->assertSee($data['anotherProduct']->name)
            ->assertDontSee($data['products'][0]->name);
    }

    /** @test */
    public function it_filter_draft_products()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 4, 1, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('status', 1)
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['anotherProduct']->name);
    }

    /** @test */
    public function it_filter_draft_and_publish_products()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 4, 1, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('status', 'all')
            ->assertSee($data['products'][0]->name)
            ->assertSee($data['anotherProduct']->name);
    }

    /** @test */
    public function it_filter_category_products()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 12, 2, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('category', $data['category']->id)
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['anotherProduct']->name);
    }

    /** @test */
    public function it_filter_subcategory_products()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 12, 2, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('subcategory', $data['subcategory']->id)
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['anotherProduct']->name);
    }

    /** @test */
    public function it_filter_brand_products()
    {
        $data = $this->generate_product_refactor(1, false, false ,false, 0, false, 12, 2, 1, 1);

        Livewire::test(ShowProducts2::class)
            ->set('brand', $data['brand']->id)
            ->assertSee($data['products'][0]->name)
            ->assertDontSee($data['anotherProduct']->name);

    }
}
