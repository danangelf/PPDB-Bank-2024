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
        Schema::create('log_synchronize', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('table');
            $table->text('params')->nullable();
            $table->string('status_result',100);
            $table->text('result')->nullable();
            $table->text('msg_result')->nullable();
            $table->text('extra_info')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_synchronize');
    }
};
