<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            if (! Schema::hasColumn('analyses', 'total_produtos_cadastrados')) {
                $table->unsignedInteger('total_produtos_cadastrados')
                    ->default(0)
                    ->after('preco_produto_mais_barato');
            }

            if (! Schema::hasColumn('analyses', 'quantidade_total_estoque')) {
                $table->unsignedInteger('quantidade_total_estoque')
                    ->default(0)
                    ->after('total_produtos_cadastrados');
            }

            if (! Schema::hasColumn('analyses', 'valor_total_estoque')) {
                $table->decimal('valor_total_estoque', 12, 2)
                    ->default(0)
                    ->after('quantidade_total_estoque');
            }

            if (! Schema::hasColumn('analyses', 'produto_maior_estoque')) {
                $table->string('produto_maior_estoque')
                    ->nullable()
                    ->after('valor_total_estoque');
            }

            if (! Schema::hasColumn('analyses', 'produtos_sem_estoque')) {
                $table->unsignedInteger('produtos_sem_estoque')
                    ->default(0)
                    ->after('produto_maior_estoque');
            }
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            foreach ([
                'produtos_sem_estoque',
                'produto_maior_estoque',
                'valor_total_estoque',
                'quantidade_total_estoque',
                'total_produtos_cadastrados',
            ] as $column) {
                if (Schema::hasColumn('analyses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
