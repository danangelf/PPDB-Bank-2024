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
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menugroup_id')->nullable();
            $table->foreign('menugroup_id')->references('id')->on('menugroups');
            $table->string('menu_label',100);
            $table->text('menu_icon')->nullable();
            $table->text('menu_desc')->nullable();
            $table->unsignedInteger('menu_order')->default(0);
            $table->string('route', 100);
            $table->foreignUuid('action_id')->nullable();
            $table->foreign('action_id')->references('id')->on('actions');
            $table->string('type', 100);
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
