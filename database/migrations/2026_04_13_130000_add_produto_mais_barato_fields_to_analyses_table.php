<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            if (! Schema::hasColumn('analyses', 'produto_mais_barato')) {
                $table->string('produto_mais_barato')
                    ->nullable()
                    ->after('produto_mais_caro');
            }

            if (! Schema::hasColumn('analyses', 'preco_produto_mais_barato')) {
                $table->decimal('preco_produto_mais_barato', 10, 2)
                    ->nullable()
                    ->after('preco_produto_mais_caro');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            if (Schema::hasColumn('analyses', 'preco_produto_mais_barato')) {
                $table->dropColumn('preco_produto_mais_barato');
            }

            if (Schema::hasColumn('analyses', 'produto_mais_barato')) {
                $table->dropColumn('produto_mais_barato');
            }
        });
    }
};
