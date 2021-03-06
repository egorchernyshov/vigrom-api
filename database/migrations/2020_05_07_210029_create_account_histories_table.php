<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_histories', static function (Blueprint $table) {
            $table->id();
            $table->timestampTz('created_at')->useCurrent();
            $table->unsignedInteger('value');
            $table->unsignedInteger('original_value');
            $table->unsignedInteger('exchange_rate_id')->nullable();
            $table->enum('transaction_type', ['debit', 'credit']);
            $table->enum('currency', ['USD', 'RUB']);
            $table->enum('change_reason', ['stock', 'refund']);
            $table->unsignedInteger('account_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_histories');
    }
}
