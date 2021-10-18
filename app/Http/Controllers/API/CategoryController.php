<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use App\Traits\RespondsWithHttpStatus;
use App\Transformers\CategoryTransformer;
use Flugg\Responder\Responder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use RespondsWithHttpStatus;
    public function index(Responder $responder){
        return $responder->success(Category::all(), new CategoryTransformer)->respond();
    }
}
