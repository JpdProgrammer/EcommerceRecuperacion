<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MiTest extends TestCase
{
    use RefreshDatabase;
    /** @test  */
    public function product_details_page_is_working()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategory($category);
        $brand = $this->createBrand($category);
        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $this->get('/products/' . $product->slug)
            ->assertStatus(200);
    }

    /** @test */
    public function it_shows_category_details()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategory($category);
        $brand = $this->createBrand($category);
        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $this->get('/categories/' . $category->slug)
            ->assertSee($category->name)
            ->assertSee($subcategory->name)
            ->assertSee($brand->name)
            ->assertSee($product->name);
    }

    /** @test  */
    public function it_shows_size_and_color_dropdowns_in_product_details()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategorySize($category);
        $brand = $this->createBrand($category);
        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $this->get('/products/' . $product->slug)
            ->assertSee('Seleccione una talla')
            ->assertSee('Seleccione un color');
    }

    /** @test */
    public function it_shows_stock_available()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategory($category);
        $brand = $this->createBrand($category);
        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $this->get('/products/' . $product->slug)
            ->assertSee('Stock disponible:')
            ->assertSee($product->quantity);
    }

    /** @test */
    public function it_shows_stock_available_color()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategoryColor($category);
        $brand = $this->createBrand($category);
        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $this->get('/products/' . $product->slug)
            ->assertSee('Stock disponible:')
            ->assertSee($product->quantity);
    }

    /** @test */
    public function it_shows_stock_available_size()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategorySize($category);
        $brand = $this->createBrand($category);
        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $this->get('/products/' . $product->slug)
            ->assertSee('Stock disponible:')
            ->assertSee($product->quantity);
    }

    /** @test */
    public function it_has_a_item()
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategory($category);
        $brand = $this->createBrand($category);

        $product = $this->createProduct($subcategory, $brand);
        $this->createImages($product);

        $product2 = $this->createProduct($subcategory, $brand);
        $this->createImages($product2);

        $this->createCart($product, 1);

        $this->get('/shopping-cart')
            ->assertSee($product->name)
            ->assertDontSee($product2->name);
    }
}
