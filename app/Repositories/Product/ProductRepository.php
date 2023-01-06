<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Repository;

interface ProductRepository extends Repository
{

    public function getProduct($request);
    public function getReview();
    public function getResponse();
    // Write something awesome :)
}

//nac
