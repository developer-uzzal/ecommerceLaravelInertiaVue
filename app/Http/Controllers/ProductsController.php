<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Laravel\Pail\File;
use App\Models\Product;
use App\FileUploadMedia;
use App\Models\Category;
use App\Models\ProductSize;
use Illuminate\Support\Str;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use function Pest\Laravel\get;
use function Pest\Laravel\json;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductVariantsAttributeValue;

class ProductsController extends Controller
{

    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'message' => $products,
        ], 200);
    }

    public function allProducts(Request $request)
    {
        $page = $request->get('page', 1); // Default to page 1
        $perPage = 20;

        $cacheKey = "all_products_page_{$page}";

        $products = Cache::remember($cacheKey, 60 * 24, function () use ($perPage) {
            return Product::where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });

        return response()->json([
            'status' => 'success',
            'message' => $products,
        ], 200);
    }

    public function allNewTrendingProducts()
    {
        $products = Cache::remember('all_new_products', 60 * 24, function () {
            return Product::with('variants.attributeValues.attribute', 'images')
                ->where('is_active', 1)
                ->where('trending', 1)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'message' => $products,
        ], 200);
    }

    public function allFeaturedProducts()
    {
        $products = Cache::remember('all_featured_products', 60 * 24, function () {
            return Product::with(['colors', 'sizes', 'images', 'category'])
                ->where('is_active', 1)
                ->where('featured', 1)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'message' => $products,
        ], 200);
    }


    public function newAllProductsByTrendOrFeatured(Request $request, $name)
{
    $validTypes = ['trending', 'featured'];

    if (!in_array($name, $validTypes)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid product type requested.',
        ], 400);
    }

    $page = $request->get('page', 1);
    $perPage = 8;
    $cacheKey = "products_{$name}_page_{$page}_limit_{$perPage}";

    $products = Cache::remember($cacheKey, now()->addMinutes(60 * 24), function () use ($name, $perPage) {
        $query = Product::with(['colors', 'sizes', 'images', 'category'])
            ->where('is_active', 1);

        if ($name === 'trending') {
            $query->where('trending', 1);
        } elseif ($name === 'featured') {
            $query->where('featured', 1);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    });

    return response()->json([
        'status' => 'success',
        'message' => $products,
    ], 200);
}


    public function productBySlug($slug)
    {
        $cacheKey = 'product_by_slug_' . $slug;

        $data = Cache::remember($cacheKey, 60 * 24, function () use ($slug) {
            $product = Product::with(['colors', 'sizes', 'images', 'category'])
                ->where('slug', $slug)
                ->where('is_active', 1)
                ->first();

            if (!$product) {
                return null;
            }

            // Related products from the same category (excluding the current one)
            $relatedProducts = Product::with(['images'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', 1)
                ->latest()
                ->take(4)
                ->get();

            return [
                'product' => $product,
                'related_products' => $relatedProducts
            ];
        });

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => $data,
        ], 200);
    }



    public function productByCategorySlug($slug)
    {
        $page = request()->get('page', 1);
        $perPage = 10;
        $cacheKey = "products_by_category_slug_{$slug}_page_{$page}";

        $products = Cache::remember($cacheKey, 60 * 24, function () use ($slug, $perPage) {
            $category = Category::where('slug', $slug)->first();

            if (!$category) {
                return null;
            }

            return $category->products()
                ->where('is_active', 1)
                ->with(['colors', 'sizes', 'images'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        });

        if (!$products) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => $products,
        ], 200);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->query('search');

        try {
            $products = Product::with('category', 'brand')
                ->where('title', 'like', "%$search%")
                ->orWhere('slug', 'like', "%$search%")
                ->orWhere('short_des', 'like', "%$search%")
                ->orWhere('long_des', 'like', "%$search%")
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => [
                    'data' => $products
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        // return response()->json([
        //     'status' => 'error',
        //     'message' => $request->all(),
        // ],200);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'rPrice' => 'required|numeric',
                'sPrice' => 'required|numeric',
                'qty' => 'required|integer',
                'discount' => 'nullable|numeric',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'sizes' => 'nullable',
                'colors' => 'nullable',

            ]);

            DB::beginTransaction();

            $slug = str_replace(' ', '-', strtolower($request->name));
            // Upload thumbnail
            $thumbnailPath = FileUploadMedia::upload($request->file('thumbnail'), $slug, 'products', 'public');

            // Create product
            $product = Product::create([
                'title' => $request->name,
                'slug' => $slug,
                'short_des' => $request->short_description,
                'long_des' => $request->description,
                'base_price' => $request->rPrice,
                'sale_price' => $request->sPrice,
                'stock' => $request->qty,
                'discount' => $request->discount,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'image' => $thumbnailPath,
                'is_active' => $request->is_active ?? 0,
                'featured' => $request->featured ?? 0,
                'trending' => $request->trending ?? 0,
                'best_selling' => $request->best_selling ?? 0,
            ]);

            // Upload multiple images
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $index => $imageFile) {
                    $imageFilename = $slug . '-gallery-' . $index . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
                    $imagePath = $imageFile->storeAs('products-images', $imageFilename, 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'product_image' => $imagePath,
                    ]);
                }
            }

            $sizes = json_decode($request->sizes);

            if ($sizes) {

                foreach ($sizes as $size) {

                    ProductSize::create([
                        'product_id' => $product->id,
                        'size_id' => $size
                    ]);
                }
            }

            $colors = json_decode($request->colors);

            if ($colors) {

                foreach ($colors as $color) {

                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_id' => $color
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully.',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),

            ], 500);
        }
    }

    // public function storeVariant(Request $request)
    // {


    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'short_description' => 'nullable|string',
    //         'description' => 'nullable|string',
    //         'rPrice' => 'required|numeric',
    //         'sPrice' => 'nullable|numeric',
    //         'qty' => 'required|integer',
    //         'discount' => 'nullable|numeric',
    //         'category_id' => 'required|exists:categories,id',
    //         'brand_id' => 'required|exists:brands,id',
    //         'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    //         'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

    //     ]);

    //     try {

    //         DB::beginTransaction();

    //         $slug = str_replace(' ', '-', strtolower($request->name));
    //         // Upload thumbnail
    //         $thumbnailPath = FileUploadMedia::upload($request->file('thumbnail'), $slug, 'products', 'public');

    //         // Create product
    //         $product = Product::create([
    //             'title' => $request->name,
    //             'slug' => $slug,
    //             'short_des' => $request->short_description,
    //             'long_des' => $request->description,
    //             'base_price' => $request->rPrice,
    //             'sale_price' => $request->sPrice,
    //             'stock' => $request->qty,
    //             'discount' => $request->discount,
    //             'category_id' => $request->category_id,
    //             'brand_id' => $request->brand_id,
    //             'image' => $thumbnailPath,
    //             'is_active' => $request->is_active,
    //             'featured' => $request->featured,
    //             'trending' => $request->trending,
    //             'best_selling' => $request->best_selling,
    //         ]);

    //         // Upload multiple images
    //         if ($request->hasFile('images')) {

    //             foreach ($request->file('images') as $index => $imageFile) {
    //                 $imageFilename = $slug . '-gallery-' . $index . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
    //                 $imagePath = $imageFile->storeAs('products-images', $imageFilename, 'public');

    //                 ProductImage::create([
    //                     'product_id' => $product->id,
    //                     'product_image' => $imagePath,
    //                 ]);
    //             }
    //         }



    //         $colors = collect($request->color)->map(function ($item) {
    //             return json_decode($item, true);
    //         });

    //         $sizes = collect($request->sizes)->map(function ($item) {
    //             return json_decode($item, true);
    //         })->values()->all();

    //         // return $colors . $sizes;

    //         $colorLabel = $colors->first()['label'];
    //         $colorId = $colors->first()['value'];

    //         // $sku = $colorLabel . '-' . $sizes[0]['label'];





    //         $productVariantId = '';

    //         for ($i = 0; $i < count($sizes); $i++) {

    //             $baseSku = $colorLabel . '-' . $sizes[$i]['label'];
    //             $sku = $baseSku . '-' . Str::random(6) . '-' . ($i + 1);


    //             $productVariant = ProductVariant::create([
    //                 'product_id' => $product->id,
    //                 'sku' => $sku,
    //                 'stock' => $sizes[$i]['stock'],
    //                 'price' => $sizes[$i]['price']
    //             ]);

    //             $productVariantId = $productVariant->id;

    //             $sizes[$i]['product_variant_id'] = $productVariantId;
    //             $sizes[$i]['attribute_value_id'] = $sizes[$i]['value'];

    //             $PVAV = ProductVariantsAttributeValue::create([
    //                 'product_variant_id' => $productVariantId,
    //                 'attribute_value_id' => $colorId
    //             ]);

    //             if (!$PVAV->exists()) {
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'message' => 'Color already exists',
    //                 ]);
    //             }
    //         }


    //         DB::commit();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Product created successfully.',
    //         ], 201);
    //     } catch (Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),

    //         ], 500);
    //     }
    // }

    public function storeVariant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'rPrice' => 'required|numeric',
            'sPrice' => 'nullable|numeric',
            'qty' => 'required|integer',
            'discount' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $slug = str_replace(' ', '-', strtolower($request->name));

            // Upload thumbnail
            $thumbnailPath = FileUploadMedia::upload(
                $request->file('thumbnail'),
                $slug,
                'products',
                'public'
            );

            // Create product
            $product = Product::create([
                'title' => $request->name,
                'slug' => $slug,
                'short_des' => $request->short_description,
                'long_des' => $request->description,
                'base_price' => $request->rPrice,
                'sale_price' => $request->sPrice,
                'stock' => $request->qty,
                'discount' => $request->discount,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'image' => $thumbnailPath,
                'is_active' => $request->is_active,
                'featured' => $request->featured,
                'trending' => $request->trending,
                'best_selling' => $request->best_selling,
            ]);

            // Upload multiple gallery images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $imageFilename = $slug . '-gallery-' . $index . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
                    $imagePath = $imageFile->storeAs('products-images', $imageFilename, 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'product_image' => $imagePath,
                    ]);
                }
            }

            // Handle variants with color and sizes
            $colors = collect($request->color)->map(function ($item) {
                return json_decode($item, true);
            });

            $sizes = collect($request->sizes)->map(function ($item) {
                return json_decode($item, true);
            })->values()->all();

            $colorLabel = $colors->first()['label'];
            $colorId = $colors->first()['value'];

            for ($i = 0; $i < count($sizes); $i++) {
                $sizeLabel = $sizes[$i]['label'];
                $sizeValueId = $sizes[$i]['value'];

                $baseSku = $colorLabel . '-' . $sizeLabel;
                $sku = $baseSku . '-' . Str::random(6) . '-' . ($i + 1);

                // Create variant
                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'stock' => $sizes[$i]['stock'],
                    'price' => $sizes[$i]['price']
                ]);

                // Attach color
                ProductVariantsAttributeValue::create([
                    'product_variant_id' => $productVariant->id,
                    'attribute_value_id' => $colorId
                ]);

                // Attach size
                ProductVariantsAttributeValue::create([
                    'product_variant_id' => $productVariant->id,
                    'attribute_value_id' => $sizeValueId
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Product with variants created successfully.',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




    public function show($id)
    {
        $product = Product::with('images', 'category', 'brand', 'colors', 'sizes')->find($id);
        return response()->json([
            'status' => 'success',
            'message' => $product,
        ], 200);
    }



    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'short_description' => 'nullable|string',
                'description' => 'nullable|string',
                'rPrice' => 'required|numeric',
                'sPrice' => 'required|numeric',
                'qty' => 'required|integer',
                'discount' => 'nullable|numeric',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'sizes' => 'nullable',
                'colors' => 'nullable',

            ]);

            DB::beginTransaction();

            $slug = str_replace(' ', '-', strtolower($request->name));

            $product = Product::where('id', $id)->first();

            if ($request->hasFile('thumbnail')) {

                $thumbnailPath = FileUploadMedia::upload($request->file('thumbnail'), $slug, 'products', 'public', $request->oldImage);
            }



            $product->update([
                'title' => $request->name,
                'slug' => $slug,
                'short_description' => $request->short_description,
                'long_des' => $request->description,
                'rPrice' => $request->rPrice,
                'sPrice' => $request->sPrice,
                'qty' => $request->qty,
                'discount' => $request->discount,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'thumbnail' => $thumbnailPath ?? $request->oldImage,
                'is_active' => $request->is_active,
                'featured' => $request->featured,
                'trending' => $request->trending,
                'best_selling' => $request->best_selling,
            ]);

            // Upload multiple images
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $index => $imageFile) {
                    $imageFilename = $slug . '-gallery-' . $index . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
                    $imagePath = $imageFile->storeAs('products-images', $imageFilename, 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'product_image' => $imagePath,
                    ]);
                }
            }

            $sizes = json_decode($request->sizes);

            ProductSize::where('product_id', $product->id)->delete();

            if ($sizes) {

                foreach ($sizes as $size) {

                    ProductSize::create([
                        'product_id' => $product->id,
                        'size_id' => $size
                    ]);
                }
            }

            $colors = json_decode($request->colors);

            ProductColor::where('product_id', $product->id)->delete();

            if ($colors) {

                foreach ($colors as $color) {

                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_id' => $color
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully.',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),

            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }

            $productImage = $product->image;
            $productId = $product->id;

            $images = ProductImage::where('product_id', $productId)->get();

            foreach ($images as $image) {
                // Assuming DB has 'storage/product_images/filename.jpg'
                $imagePath = str_replace('storage/', '', $image->product_image);

                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            if ($productImage) {
                $productImage = str_replace('storage/', '', $productImage);
                Storage::disk('public')->delete($productImage);
            }

            ProductImage::where('product_id', $productId)->delete();
            ProductVariant::where('product_id', $productId)->delete();

            $product->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    function delateRelatedImage(Request $request, $id)
    {
        $productId = $id;
        $product = ProductImage::where('id', $productId)->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not image found'
            ], 404);
        } else {

            $imagePath = str_replace('storage/', '', $product->product_image);

            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product thumbnail updated successfully.',
            ], 200);
        }
    }
}
