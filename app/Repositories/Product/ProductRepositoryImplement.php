<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Http\Controllers\BaseController;
use App\Models\products;
use App\Models\reviews;
use DB;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;

class ProductRepositoryImplement extends Eloquent implements ProductRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(products $model)
    {
        $this->model = $model;
    }

    public function getProduct($request)
    {
        $by = $request->query('by');
        $search = $request->query('search');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = $this->model->with(['reviews']);
        if (isset($by) && !empty($by)) :
            $query = $query->where($by, 'like', '%' . $search . '%');
        endif;
        if (isset($from) && !empty($from)) :
            $query = $query->whereBetween('date', [$from, $to]);
        endif;
        $query = $query->get();

        if ($query->isEmpty()) {
            return BaseController::Error(NULL, 'Data not found', 400);
        }

        return BaseController::success($query, "Sukses menarik data Product", 200);
    }
    public function getReview()
    {
        $query = reviews::get();

        if ($query->isEmpty()) {
            return BaseController::Error(NULL, 'Data not found', 400);
        }

        return BaseController::success($query, "Sukses menarik data Review", 200);
    }
    public function getResponse()
    {
        //Total Review
        $total_review = reviews::count();
        //Rata-Rata rating
        $avgStar = reviews::avg('rating');
        //Hitung Product
        $products = $this->model->count();

        //container $ratings
        $result = [];
        //container $ratingCount
        $result2 = [];
        //container $averageRating
        $result3 = [];
        //container allStar
        $result4 = [];

        //count All star
        $allStar = reviews::join('products', 'products.id', '=', 'reviews.product_id')
            ->select('reviews.rating as star', DB::raw('count(reviews.id) as count'))
            ->groupBy('reviews.rating')
            ->orderBy('star')
            ->get();

        for ($i = 1; $i <= $products; $i++) {
            //GroupBy Rating/count per rating
            $ratings = reviews::join('products', 'products.id', '=', 'reviews.product_id')
                ->select('products.id as id', 'reviews.rating as star', 'products.name', DB::raw('count(reviews.id) as count'))
                ->where('product_id', '=', $i)
                ->groupBy('reviews.rating')
                ->orderBy('products.name')
                ->orderBy('reviews.rating')
                ->get();
            //Count total review per Product
            $ratingCount = DB::table('reviews')->join('products', 'products.id', '=', 'reviews.product_id')
                ->where('reviews.product_id', $i)
                ->select('products.id as id', 'products.name', DB::raw('count(reviews.id) as total_review'))
                ->get();

            //average total per Product
            $averageRating = DB::table('reviews')->join('products', 'products.id', '=', 'reviews.product_id')
                ->where('reviews.product_id', $i)
                ->select('products.id as id', 'products.name', DB::raw('avg(reviews.rating) as ratings'))
                ->get();

            //Contain Result
            $result[$i] = $ratings;
            $result2[$i] = $ratingCount;
            $result3[$i] = $averageRating;
        }
        //Merging Array
        $array = array_merge($result, $result2, $result3);

        //declare new array
        $query = array();

        //looping untuk dapat smua data per product
        for ($x = 1; $x <= $products; $x++) {
            $query[] = array(
                "Product Name" => $array[$x - 1][0]->name,
                "total_reviews" => $array[$x + 4][0]->total_review,
                "average_ratings" => $array[$x + 9][0]->ratings,
                "5_star" => $array[$x - 1][4]->count,
                "4_star" => $array[$x - 1][3]->count,
                "3_star" => $array[$x - 1][2]->count,
                "2_star" => $array[$x - 1][1]->count,
                "1_star" => $array[$x - 1][0]->count,

            );
        }
        //array baru untuk return
        $starArray = array();

        //buat array
        $starArray[] = array(

            "5_star" => $allStar[4]->count,
            "4_star" => $allStar[3]->count,
            "3_star" => $allStar[2]->count,
            "2_star" => $allStar[1]->count,
            "1_star" => $allStar[0]->count,

        );


        //grouping data sebelum kirim
        $summary = [
            "total_reviews" => $total_review,
            "average_ratings" => $avgStar,
            "star" => $starArray,
            "product" => $query
        ];


        return BaseController::success($summary, "Sukses Menarik data", 200);
    }
    // Write something awesome :)
}
