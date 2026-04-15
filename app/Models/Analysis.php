<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    protected $casts = [
        'preco_produto_mais_caro' => 'decimal:2',
        'preco_produto_mais_barato' => 'decimal:2',
        'total_produtos_cadastrados' => 'integer',
        'quantidade_total_estoque' => 'integer',
        'valor_total_estoque' => 'decimal:2',
        'produtos_sem_estoque' => 'integer',
    ];
    
    protected $fillable = [
        'produto_mais_vendido',
        'produto_mais_caro',
        'produto_mais_barato',
        'preco_produto_mais_caro',
        'preco_produto_mais_barato',
        'total_produtos_cadastrados',
        'quantidade_total_estoque',
        'valor_total_estoque',
        'produto_maior_estoque',
        'produtos_sem_estoque',
    ];
}
