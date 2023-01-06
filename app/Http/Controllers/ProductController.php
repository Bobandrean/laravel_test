<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Product\ProductRepository;


class ProductController extends Controller
{
    private $ProductRepository;

    public function __construct(ProductRepository $ProductRepository)
    {
        $this->ProductRepository = $ProductRepository;
    }

    public function index(Request $request)
    {
        return $this->ProductRepository->getProduct($request);
    }
    public function indexReview()
    {
        return $this->ProductRepository->getReview();
    }
    public function response()
    {
        return $this->ProductRepository->getResponse();
    }
}
