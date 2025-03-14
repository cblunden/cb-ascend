<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('book_copies', function (Blueprint $table) {
            $table->after('status', function (Blueprint $table) {
                $table->string('aisle');
                $table->string('shelf');
                $table->unsignedInteger('number');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_copies', function (Blueprint $table) {
            $table->dropColumn('aisle', 'shelf', 'number');
        });
    }
};
