<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Product;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
    ];

    // Venda pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Venda tem vários produtos
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}