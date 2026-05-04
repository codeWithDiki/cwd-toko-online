<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->string("thumbnail_url")->nullable();
            $table->longText("description")->nullable();
            $table->boolean("is_active")->default(false);

            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.brand_class'))->nullable();
            $table->string("name");
            $table->string("slug")->unique();
            $table->string("type");
            $table->longText("description")->nullable();
            $table->double("price");
            $table->double("discount_price")->nullable();
            $table->integer("stock")->default(0);
            $table->string("sku")->nullable()->unique();
            $table->boolean("is_active")->default(false);
            $table->longText("tags")->nullable();
            $table->longText("meta_data")->nullable();

            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug")->unique();
            $table->string("thumbnail_url")->nullable();
            $table->longText("description")->nullable();
            $table->boolean("is_active")->default(false);

            $table->timestamps();
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.category_class'));
            $table->foreignIdFor(config('product-module.product_class'));

            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.product_class'));
            $table->string("name");
            $table->string("slug")->unique();
            $table->longText("description")->nullable();
            $table->double("price");
            $table->double("discount_price")->nullable();
            $table->integer("stock")->default(0);
            $table->string("sku")->nullable();
            $table->boolean("is_active")->default(false);

            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.product_class'));
            $table->string("image_url");
            $table->boolean("is_primary");

            $table->timestamps();
        });

        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.product_class'));
            $table->string("label");
            $table->string("color_hex");
            $table->integer("stock")->default(0);
            $table->boolean("is_active")->default(false);
            $table->boolean("is_primary")->default(false);

            $table->timestamps();
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.product_class'));
            $table->string("label");
            $table->string("value");
            $table->integer("stock")->default(0);
            $table->boolean("is_active")->default(false);
            $table->boolean("is_primary")->default(false);

            $table->timestamps();
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.product_class'));
            $table->string("from");
            $table->double("rating");
            $table->string("message")->nullable();

            $table->timestamps();
        });

        Schema::create('product_wrappers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('product-module.product_class'));
            $table->foreignIdFor(config('product-module.product_variant_class'))->nullable();
            $table->foreignIdFor(config('product-module.product_size_class'))->nullable();
            $table->foreignIdFor(config('product-module.product_color_class'))->nullable();

            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists("product_wrappers");
        Schema::dropIfExists("product_reviews");
        Schema::dropIfExists("product_sizes");
        Schema::dropIfExists("product_colors");
        Schema::dropIfExists("product_images");
        Schema::dropIfExists("product_variants");
        Schema::dropIfExists("category_product");
        Schema::dropIfExists("categories");
        Schema::dropIfExists("products");
        Schema::dropIfExists("brands");
    }

};
