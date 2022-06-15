<?php

namespace Tests;

use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\AddCartItemColor;
use App\Http\Livewire\AddCartItemSize;
use App\Http\Livewire\Admin\CreateProduct;
use App\Http\Livewire\CreateOrder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Department;
use App\Models\District;
use App\Models\Image;
use App\Models\Order;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

trait CreateData
{
    use WithFaker;

    // generate_product_new y generate_product_to_search sirven para pasasr los tests de la carpeta Examen pero solo pasan si se ejecutan 1 a 1
    public function generate_product_new($products = 1, $addToCart = false, $createOrder = false, $color = false, $colorQuantity = 0, $size = false, $productQuantity = 10,  $productStatus = 2, $envioType = 1, $anotherProduct = false, $users = 1, $userLogin = true, $userId = 1) {

        if (User::all()->count() === 0 && $users != 0) {
            User::factory($users)->create();
        }

        if ($userLogin === true && $users != 0) {
            $this->actingAs(User::find($userId));
        }

        $originalProducts = Product::all()->count();

        $category = Category::factory()->create();

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size,
        ]);

        $brand = Brand::factory()->create();
        $brand->categories()->attach($category->id);

        Product::factory($products)->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => $productQuantity,
            'status' => $productStatus
        ])->each(function(Product $product){
            Image::factory(4)->create([
                'imageable_id' => $product->id,
                'imageable_type' => Product::class
            ]);
        });

        if ($color) {
            Color::create([
                'name' => $this->faker->sentence(1),
            ]);
            foreach (Product::all() as $product) {
                $product->colors()->attach([
                    1 => [
                        'quantity' => $colorQuantity,
                    ],
                ]);
            }
        }

        if ($size) {
            foreach (Product::all() as $product) {
                $product->sizes()->create([
                    'name' => $this->faker->sentence(1),
                ]);

                Size::first()->colors()
                    ->attach([
                        1 => [
                            'quantity' => $colorQuantity,
                        ],
                    ]);
            }
        }

        if ($addToCart) {
            for ($i = 1; $i <= $products; $i++) {

                $product = Product::find($originalProducts + $i);

                if ($size) {
                    Livewire::test(AddCartItemSize::class, ['product' => $product])
                        ->set('size_id', 1)
                        ->set('color_id', 1)
                        ->call('addItem');

                } else if($color) {
                    Livewire::test(AddCartItemColor::class, ['product' => $product])
                        ->set('color_id', 1)
                        ->call('addItem');

                } else {
                    Livewire::test(AddCartItem::class, ['product' => $product])
                        ->call('addItem');
                }
            }
        }

        if ($createOrder) {
            Livewire::test(CreateOrder::class)
                ->set('contact', 'Contacto')
                ->set('phone', '123456789')
                ->set('envio_type', $envioType)
                ->set('shipping_cost', 1)
                ->call('create_order');
        }

        if ($anotherProduct) {
            $category = Category::factory()->create();

            $subcategory = Subcategory::factory()->create([
                'category_id' => $category->id,
                'color' => $color,
                'size' => $size,
            ]);

            $brand = Brand::factory()->create();
            $brand->categories()->attach($category->id);

            Product::factory()->create([
                'subcategory_id' => $subcategory->id,
                'brand_id' => $brand->id,
                'quantity' => $productQuantity,
                'status' => $productStatus
            ])->each(function(Product $product){
                Image::factory(4)->create([
                    'imageable_id' => $product->id,
                    'imageable_type' => Product::class
                ]);
            });

            if ($color) {
                Color::create([
                    'name' => $this->faker->sentence(1),
                ]);
                foreach (Product::all() as $product) {
                    $product->colors()->attach([
                        1 => [
                            'quantity' => $colorQuantity,
                        ],
                    ]);
                }
            }

            if ($size) {
                foreach (Product::all() as $product) {
                    $product->sizes()->create([
                        'name' => $this->faker->sentence(1),
                    ]);

                    Size::first()->colors()
                        ->attach([
                            1 => [
                                'quantity' => $colorQuantity,
                            ],
                        ]);
                }
            }
        }
    }

    public function generate_product_to_search($productName = 'pepe') {

        $category = Category::factory()->create();

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false,
        ]);

        $brand = Brand::factory()->create();
        $brand->categories()->attach($category->id);

        $productsCount = Product::all()->count();

        Product::factory()->create([
            'name' => $productName
        ]);
        Image::factory(4)->create([
            'imageable_id' => Product::find($productsCount+1)->id,
            'imageable_type' => Product::class,
        ]);

        $productsCount++;

        Product::factory()->create();
        Image::factory(4)->create([
            'imageable_id' => Product::find($productsCount+1)->id,
            'imageable_type' => Product::class,
        ]);

    }

    // estos tres métodos los he refactorizado para que pasen los tests de la carpeta Examen2 todos a la vez sin que de fallos
    public function generate_product_refactor($products = 1, $addToCart = false, $createOrder = false, $color = false, $colorQuantity = 0, $size = false, $productQuantity = 10,  $productStatus = 2, $envioType = 1, $anotherProduct = false, $users = 1, $userLogin = true, $userId = 1)
    {
        $data = [];

        if (User::all()->count() === 0 && $users != 0) {
            $data['users'] = User::factory($users)->create();
        }

        if ($userLogin === true && $users != 0) {
            foreach (User::all() as $key => $userAll) {
                if (($key + 1) === $userId) {
                    $user = $userAll;
                }
            }
            $this->actingAs($user);
            $data['userActive'] = $user;
        }

        $originalProducts = Product::all()->count();

        $category = Category::factory()->create();
        $data['category'] = $category;

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size,
        ]);
        $data['subcategory'] = $subcategory;

        $brand = Brand::factory()->create();
        $brand->categories()->attach($category->id);
        $data['brand'] = $brand;

        $data['products'] = Product::factory($products)->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => $productQuantity,
            'status' => $productStatus,
            'price' => 30
        ])->each(function(Product $product){
            Image::factory(4)->create([
                'imageable_id' => $product->id,
                'imageable_type' => Product::class
            ]);
        });

        if ($color) {
            $data['color'] = Color::create([
                'name' => $this->faker->sentence(1),
            ]);
            $color_id = Color::first()->id;

            foreach (Product::all() as $product) {
                $product->colors()->attach([
                    $color_id => [
                        'quantity' => $colorQuantity,
                    ],
                ]);
            }
        }

        if ($size) {
            foreach (Product::all() as $product) {
                $product->sizes()->create([
                    'name' => $this->faker->sentence(1),
                ]);
                $size_id = Size::first()->id;

                Size::first()->colors()
                    ->attach([
                        $color_id => [
                            'quantity' => $colorQuantity,
                        ],
                    ]);
            }
            $data['size'] = Size::first();
        }

        if ($addToCart) {
            for ($i = 1; $i <= $products; $i++) {

                foreach (Product::all() as $key => $productAll) {
                    if (($key + 1) === $i) {
                        $product = $productAll;
                    }
                }

                if ($size) {
                    Livewire::test(AddCartItemSize::class, ['product' => $product])
                        ->set('size_id', $size_id)
                        ->set('color_id', $color_id)
                        ->call('addItem');

                } else if($color) {
                    Livewire::test(AddCartItemColor::class, ['product' => $product])
                        ->set('color_id', $color_id)
                        ->call('addItem');

                } else {
                    Livewire::test(AddCartItem::class, ['product' => $product])
                        ->call('addItem');
                }
            }
        }

        if ($createOrder) {
            Livewire::test(CreateOrder::class)
                ->set('contact', 'Contacto')
                ->set('phone', '123456789')
                ->set('envio_type', $envioType)
                ->set('shipping_cost', 1)
                ->call('create_order');
        }

        $data['cart'] = Cart::content();
        $data['order'] = Order::first();

        if ($anotherProduct) {
            $category = Category::factory()->create();
            $data['anotherCategory'] = $category;

            $subcategory = Subcategory::factory()->create([
                'category_id' => $category->id,
                'color' => $color,
                'size' => $size,
            ]);
            $data['anotherSubcategory'] = $subcategory;

            $brand = Brand::factory()->create();
            $brand->categories()->attach($category->id);
            $data['anotherBrand'] = $brand;

            $data['anotherProduct'] = Product::factory()->create([
                'subcategory_id' => $subcategory->id,
                'brand_id' => $brand->id,
                'quantity' => $productQuantity,
                'status' => 2,
                'price' => 20
            ]);
            Image::factory(4)->create([
                'imageable_id' => $data['anotherProduct']->id,
                'imageable_type' => Product::class
            ]);

            if ($color) {
                $data['anotherColor'] = Color::create([
                    'name' => $this->faker->sentence(1),
                ]);
                foreach (Product::all() as $product) {
                    $product->colors()->attach([
                        1 => [
                            'quantity' => $colorQuantity,
                        ],
                    ]);
                }
            }

            if ($size) {
                foreach (Product::all() as $product) {
                    $product->sizes()->create([
                        'name' => $this->faker->sentence(1),
                    ]);

                    Size::first()->colors()
                        ->attach([
                            1 => [
                                'quantity' => $colorQuantity,
                            ],
                        ]);
                }
                $data['anotherSize'] = Size::first();
            }
        }

        return $data;
    }

    public function generate_product_to_search_refactor($productName = 'pepe')
    {
        $data = [];

        $category = Category::factory()->create();
        $data['category'] = $category;

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false,
        ]);
        $data['subcategory'] = $subcategory;

        $brand = Brand::factory()->create();
        $brand->categories()->attach($category->id);
        $data['brand'] = $brand;


        $data['productSearched'] = Product::factory()->create([
            'name' => $productName
        ]);
        Image::factory(4)->create([
            'imageable_id' => $data['productSearched']->id,
            'imageable_type' => Product::class,
        ]);

        $data['product'] = Product::factory()->create();
        Image::factory(4)->create([
            'imageable_id' => $data['product']->id,
            'imageable_type' => Product::class,
        ]);

        return $data;
    }

    public function create_a_new_product($category_id, $subcategory_id, $name, $slug, $description, $brand_id, $price, $quantity){
        Role::create(['name' => 'admin']);
        $userAdmin = User::factory()->create()->assignRole('admin');
        $this->actingAs($userAdmin);

        Livewire::test(CreateProduct::class)
            ->set('category_id', $category_id)
            ->set('subcategory_id', $subcategory_id)
            ->set('name', $name)
            ->set('slug', $slug)
            ->set('description', $description)
            ->set('brand_id', $brand_id)
            ->set('price', $price)
            ->set('quantity', $quantity)
            ->call('save');
    }

}
