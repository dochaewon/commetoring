<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 카테고리 데이터 추가
        $categories = [
            ['name' => '집사 고민'],
            ['name' => '사료 고민'],
            ['name' => '그루밍']
        ];

        DB::table('categories')->insert($categories);
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
