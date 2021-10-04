<?php

use App\Models\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('product_name');
            $table->decimal('product_price', 10, 2);
            $table->text('product_content')->nullable();
            $table->unsignedInteger('cate_id');
            $table->integer('product_feature')->default(0);
            $table->decimal('product_sale', 3, 3)->default(0);
            $table->timestamps();

            $table->foreign('cate_id')->references('cate_id')->on('categories')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
