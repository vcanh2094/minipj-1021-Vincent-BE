<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use RespondsWithHttpStatus;
    public function index(){
        $categories = new CategoryCollection(Category::all());
        return $this->successWithData('Category List', $categories, 200);
    }
}
