<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Config;
use App\Models\Content;
use App\Models\Product;
use App\Models\Users;

class FrontEndController extends Controller
{
    public function footer()
    {
        $config = Config::all();
        return response()->json([
            'status' => 200,
            'config' => $config,
        ]);
    }
    public function viewproduct()
    {
        $product = Product::where([
            ['status', '1'],
        ])->get();
        $product_featured = Product::where([
            ['featured', '1'],
            ['status', '1'],
        ])
            ->get();
        $product_popular = Product::where([
            ['popular', '1'],
            ['status', '1'],
        ])
            ->get();
        return response()->json([
            'status' => 200,
            'product' => $product,
            'product_featured' => $product_featured,
            'product_popular' => $product_popular
        ]);
    }

    public function viewproductcategory($slug)
    {
        $category = Category::where('slug', $slug)->where('status', '1')->first();
        if ($category) {
            $product = Product::where([
                ['cateID', $category->id],
                ['status', '1'],
            ])->get();
            if ($product) {
                return response()->json([
                    'status' => 200,
                    'product_data' => [
                        'product' => $product,
                        'category' => $category
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Không tìm thấy sản phẩm',
                ]);
            };
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã loại sản phẩm',
            ]);
        }
    }

    public function productdetail($category_slug, $product_id)
    {
        $category = Category::where('slug', $category_slug)->where('status', '1')->first();
        $related_product = Product::where('cateID', $category->id)->where('status', '1')->get();
        if ($category) {
            $product = Product::where([
                ['id', $product_id],
                ['cateID', $category->id],
                ['status', '1'],
            ])->first();
            if ($product) {
                return response()->json([
                    'status' => 200,
                    'product' => $product,
                    'category' => $category,
                    'related_product' => $related_product
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Không tim thấy mã sản phẩm',
                ]);
            };
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã loại sản phẩm',
            ]);
        }
    }

    public function detailcontent($content)
    {
        $content = Content::where([
            ['id', $content],
            ['status', '1']
        ])->first();
        $related_content = Content::where('status', '1')->get();
        if ($content) {
            return response()->json([
                'status' => 200,
                'content' => $content,
                'related_content' => $related_content
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy tin tức'
            ]);
        }
    }

    public function getuser_w()
    {
        $userID = auth('sanctum')->user()->id;
        if ($userID != null) {
            $user = Users::where('id', $userID)->first();
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Không tìm thấy mã khách hàng"
                ]);
            }
        }
    }
    public function search($search_name)
    {
        $product = Product::where('name', 'LIKE', "%{$search_name}%")->where('status', 1)->get();

        return response()->json([
            'status' => 200,
            'product' => $product
        ]);
    }
}
