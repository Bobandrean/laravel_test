<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\reviews;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'price'
    ];

    public function reviews()
    {
        return $this->hasmany(reviews::class, 'product_id',);
    }
}
