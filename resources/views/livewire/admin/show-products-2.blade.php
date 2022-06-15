<div>
    <div>
        <x-slot name="header">
            <div class="flex items-center">
                <h2 class="font-semibold text-xl text-gray-600 leading-tight">
                    Lista de productos
                </h2>

                <x-button-link class="ml-auto" href="{{ route('admin.products.create') }}">
                    Agregar producto
                </x-button-link>
            </div>
        </x-slot>
        <!-- This example requires Tailwind CSS v2.0+ -->
        <x-table-responsive-2>


            <div class="px-6 py-4">
                <select wire:model="per_page" class="border-orange-500">
                    <option disabled selected value="">Seleccionar paginación</option>
                    <option value="10">10 productos</option>
                    <option value="15">15 productos</option>
                    <option value="20">20 productos</option>
                    <option value="50">50 productos</option>
                </select>
                <div class="flex inline-flex border-orange-500 border p-2">
                    <x-jet-dropdown>
                        <x-slot name="trigger">
                            <button>Seleccione las columnas</button>
                        </x-slot>

                        <x-slot name="content">
                            @foreach($columns as $column)
                                <div class="block">
                                    <input type="checkbox" wire:model="selectedColumns" value="{{ $column }}">
                                    <label>{{ $column }}</label>
                                </div>
                            @endforeach
                        </x-slot>
                    </x-jet-dropdown>
                </div>
                <div x-data="{ open : true }">
                    <div class="flex">
                        <button @click="open = !open" class="p-2 mt-2 border-orange-500 border">Filtros</button>
                        <div class="mt-4" wire:ignore>
                            <a class="button form-control bg-orange-500 ml-2 p-2" href="{{ request()->url() }}">Limpiar filtros</a>
                        </div>
                    </div>
                    <div x-show="open" class="mt-2">
                        <x-jet-input class="border-orange-500 border" size="35"
                                     wire:model="search"
                                     type="text"
                                     placeholder="Introduzca el nombre del producto a buscar" />
                        <div class="py-2">
                            <select wire:model="category" class="border-orange-500 border">
                                <option disabled selected value="default">Categorías:</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select wire:model="subcategory" class="border-orange-500 border">
                                <option disabled selected value="default">Subcategorías:</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                            <select wire:model="brand" class="border-orange-500 border">
                                <option disabled selected value="default">Marcas:</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-jet-input class="border-orange-500 border" size="25"
                                     wire:model="minPrice"
                                     type="text"
                                     placeholder="Precio Mínimo" />
                        <x-jet-input class="border-orange-500 border" size="25"
                                     wire:model="maxPrice"
                                     type="text"
                                     placeholder="Precio Máximo" />
                        <x-jet-input class="border-orange-500 border" size="25"
                                     wire:model="from"
                                     type="text"
                                     id="newDate"
                                     placeholder="Desde" />
                        <x-jet-input class="border-orange-500 border" size="25"
                                     wire:model="to"
                                     type="text"
                                     id="newDate"
                                     placeholder="Hasta" />
                        <div class="py-2">
                            <select wire:model="status" class="border-orange-500 border">
                                <option disabled selected value="default">Estado</option>
                                <option value="all">Cualquiera</option>
                                <option value="1">Borrador</option>
                                <option value="2">Publicado</option>
                            </select>
                            <x-jet-input class="border-orange-500 border" size="25"
                                         wire:model="searchSize"
                                         type="text"
                                         placeholder="Talla" />
                        </div>
                        <div>
                            <p><b>Stock: </b></p>
                            @foreach($quantities as $stock)
                                <label for="">{{ 'Más de ' . $stock }}</label>
                                <input type="radio" name="stock" class="mr-2" wire:model="stock" value="{{ $stock }}">
                            @endforeach
                            Más de
                            <x-jet-input class="border-orange-500 border text-center" size="4"
                                         wire:model="stock"
                                         type="text"
                                         placeholder="Stock" />
                        </div>
                </div>
            </div>

            @if($products->count())
                <table class="min-w-full overflow-x-auto divide-y divide-gray-200 block whitespace-nowrap">
                    <thead class="bg-gray-50">
                    <tr>
                        @if(in_array('nombre', $selectedColumns))
                            <th wire:click="sort('products.name')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Nombre</button>
                            </th>
                        @endif
                        @if(in_array('vendido', $selectedColumns))
                             <th wire:click="sort('sold')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 <button>Vendido</button>
                             </th>
                        @endif
                        @if(in_array('preVendido', $selectedColumns))
                             <th wire:click="sort('preSold')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 <button>Reservado</button>
                             </th>
                        @endif
                        @if(in_array('categoria', $selectedColumns))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Categoría</button>
                            </th>
                        @endif
                        @if(in_array('subcategoria', $selectedColumns))
                            <th wire:click="sort('subcategories.name')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Subcategoría</button>
                            </th>
                        @endif
                        @if(in_array('marca', $selectedColumns))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Marca</button>
                            </th>
                        @endif
                        @if(in_array('estado', $selectedColumns))
                            <th wire:click="sort('products.status')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Estado</button>
                            </th>
                        @endif
                        @if(in_array('precio', $selectedColumns))
                            <th wire:click="sort('products.price')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Precio</button>
                            </th>
                        @endif
                        @if(in_array('stock', $selectedColumns))
                            <th wire:click="sort('products.quantity')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Stock</button>
                            </th>
                        @endif
                        @if(in_array('creado-el', $selectedColumns))
                            <th wire:click="sort('products.created_at')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Creado el:</button>
                            </th>
                        @endif
                        @if(in_array('colores', $selectedColumns))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Colores</button>
                            </th>
                        @endif
                        @if(in_array('tallas', $selectedColumns))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <button>Tallas</button>
                            </th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr>
                            @if(in_array('nombre', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="{{ $product->images->count() ? Storage::url($product->images->first()->url) :
                                'img/default.jpg'}}" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $product->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            @if(in_array('vendido', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-center text-gray-900">{{ $product->sold }}</div>
                                </td>
                            @endif
                            @if(in_array('preVendido', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-center text-gray-900">{{ $product->preSold }}</div>
                                </td>
                            @endif
                            @if(in_array('categoria', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $product->subcategory->category->name }}</div>
                                </td>
                            @endif
                            @if(in_array('subcategoria', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $product->subcategory->name }}</div>
                                </td>
                            @endif
                            @if(in_array('marca', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $product->brand->name }}</div>
                                </td>
                            @endif
                            @if(in_array('estado', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $product->status == 1 ? 'red' : 'green'
                        }}-100 text-{{ $product->status == 1 ? 'red' : 'green'}}-800">
                            {{ $product->status == 1 ? 'Borrador' : 'Publicado' }}
                        </span>
                                </td>
                            @endif
                            @if(in_array('precio', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->price }}&euro;
                                </td>
                            @endif
                            @if(in_array('stock', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->stock }}
                                </td>
                            @endif
                            @if(in_array('creado-el', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->created_at }}
                                </td>
                            @endif
                            @if(in_array('colores', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($product->colors->count())
                                        @foreach($product->colors as $color)
                                            {{ __(ucfirst($color->name)).' '.$color->pivot->quantity }}
                                        @endforeach
                                    @else
                                        No tiene color
                                    @endif
                                </td>
                            @endif
                            @if(in_array('tallas', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($product->sizes->count())
                                        @foreach($product->sizes as $size)
                                            <b>{{ ucfirst($size->name) }} </b>
                                            @foreach($size->colors as $color)
                                                {{ __(ucfirst($color->name)).' '.$color->pivot->quantity }}
                                            @endforeach
                                        @endforeach
                                    @else
                                        No tiene tallas
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-4">
                    No existen productos coincidentes.
                </div>
            @endif

            @if($products->hasPages())
                <div class="px-6 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </x-table-responsive-2>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            flatpickr('#newDate', {
                dateFormat: 'd/m/Y',
                altFormat: 'd/m/Y',
            });
        });
    </script>
@endpush
