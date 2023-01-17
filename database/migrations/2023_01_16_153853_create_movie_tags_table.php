<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::table('movies', function (Blueprint $table) {
			$table->dropColumn('tags');
		});

		Schema::create('movie_tags', function (Blueprint $table) {
			$table->id();
			$table->foreignId('movie_id')->references('id')->on('movies')
				->onDelete('cascade');
			$table->foreignId('tag_id')->references('id')->on('tags')->onDelete('cascade');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('movie_tags');
	}
};
