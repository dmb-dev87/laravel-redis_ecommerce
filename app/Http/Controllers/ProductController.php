<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;  

class ProductController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $tags = explode(',', $request->get('tags'));
        $productId = self::getProductId();

        if (self::newProduct($productId, [
            'name' => $request->get('product_name'),
            'image' => $request->get('product_image'),
            'product_id' => $productId
        ])) {
            self::addToTags($tags);
            self::addToProductTags($productId, $tags);
            self::addProductToTags($productId, $tags);
        }

        return redirect()->route('product.all');
    }

    public function list(Request $request)
    {
        if ($request->has('tag')) {
            $products = self::getProductByTags(($request->get('tag')));
        } else {
            $products = self::getProducts();
        }
        $tags = Redis::sMembers('tags');

        return view('products.list')->with(['products' => $products, 'tags' => $tags]);
    }

    static function getProductId()
    {
        if (!Redis::exists('product_count'))
            Redis::set('product_count', 0);

        return Redis::incr('product_count');
    }

    static function newProduct($productId, $data): bool
    {
        self::addToProducts($productId);

        Redis::hMset("product:$productId", $data);

        return true;
    }

    static function addToProducts($productId): void
    {
        Redis::zAdd('products', time(), $productId);
    }

    static function addToTags(array $tags)
    {
        Redis::sAddArray('tags', $tags);
    }

    static function addToProductTags($productId, $tags)
    {
        Redis::sAddArray("product:$productId:tags", $tags);
    }

    static function addProductToTags($productId, $tags)
    {
        foreach ($tags as $tag) {
            Redis::rPush($tag, $productId);
        }
    }

    static function getProducts($start = 0, $end = -1): array
    {
        $productIds = Redis::zRange('products', $start, $end, true);
        $products = [];

        foreach ($productIds as $productId => $score) {
            $products[$score] = Redis::hGetAll("product:$productId");
        }

        return $products;
    }

    static function getProductByTags($tag, $start = 0, $end = -1): array
    {
        $productIds = Redis::lRange($tag, $start, $end);
        $products = [];

        foreach ($productIds as $productId) {
            $products[] = Redis::hGetAll("product:$productId");
        }
        return $products;
    }
}
