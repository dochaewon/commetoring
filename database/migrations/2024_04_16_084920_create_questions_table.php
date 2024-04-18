<?php

// database/migrations/create_questions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->integer('category_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
