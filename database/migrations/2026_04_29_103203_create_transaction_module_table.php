<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email")->nullable()->unique();
            $table->string("phone_number")->nullable();
            $table->string("address")->nullable();

            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('transaction-module.customer_class'));
            $table->string("trx_id")->unique();
            $table->double("total_amount");
            $table->double("tax_amount")->default(0);
            $table->double("grand_total");
            $table->string("payment_status");
            $table->string("status")->default(\CodeWithDiki\TransactionModule\Enums\TransactionStatus::ONHOLD);
            $table->longText("notes")->nullable();
            $table->dateTime("paid_at")->nullable();
            $table->dateTime("failed_at")->nullable();

            $table->timestamps();
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code");
            $table->boolean("is_active")->default(false);
            $table->string("type"); // percent or fixed
            $table->double("value");

            $table->timestamps();
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(config('transaction-module.transaction_class'));
            $table->morphs("itemable");
            $table->string("name");
            $table->string("description")->nullable();
            $table->double("price");
            $table->integer("quantity");
            $table->double("total");

            $table->timestamps();
        });

        Schema::create('transaction_logs', function(Blueprint $table){
            $table->id();
            $table->foreignIdFor(config('transaction-module.transaction_class'));
            $table->foreignIdFor(config('transaction-module.user_class'))->nullable();
            $table->string("from_status");
            $table->string("to_status");
            $table->longText("note")->nullable();

            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists("transaction_logs");
        Schema::dropIfExists("transaction_items");
        Schema::dropIfExists("discounts");
        Schema::dropIfExists("transactions");
        Schema::dropIfExists("customers");
    }
};
