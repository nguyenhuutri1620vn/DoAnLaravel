<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Content;
use App\Models\Producer;
use App\Models\Product;

class HomeController extends Controller
{
    function index()
    {
        $category = Category::where('status', '1')->get();
        $producer = Producer::where('status', '1')->get();
        $product_featured = Product::where([
            ['featured', '1'],
            ['status', '1'],
            ['number', '>', '0']
        ])
           ->get();
        $product_popular = Product::where([
            ['popular', '1'],
            ['status', '1'],
            ['number', '>', '0']
        ])
            ->get();
        $product = Product::where([
            ['status', '1'],
            ['number', '>', '0']
        ])
            ->get();
        $content = Content::where('status', '1')->get();
        return response()->json([
            'status' => 200,
            'category' => $category,
            'producer' => $producer,
            'content' => $content,
            'product_featured' => $product_featured,
            'product_popular' => $product_popular,
            'product' => $product
        ]);
    }
}
