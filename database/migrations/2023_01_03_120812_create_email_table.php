<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::create('email', function (Blueprint $table) {
			$table->id();
			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->boolean('is_primary')->default(false);
			$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->string('verification_token')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('email');
	}
};
