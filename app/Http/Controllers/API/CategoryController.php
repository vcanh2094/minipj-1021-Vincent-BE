<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Transformers\CategoryTransformer;

class CategoryController extends Controller
{
    public function index(){
        return responder()->success(Category::all(), new CategoryTransformer)->respond();
    }
}
