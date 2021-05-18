<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId("vehicle_id")->constrained("vehicles")->onUpdate('cascade')->onDelete('cascade');
            $table->enum("status", ["active", "ended"]);
            $table->foreignId("invoice_id")->nullable()->default(NULL)->constrained("invoices")->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stays');
    }
}
