<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::table('users', function (Blueprint $table) {
        $table->string('password_hash')->default('default_value')->change(); // Replace with an appropriate default value if needed
    });
    }

    public function down()
    {
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('password_hash');
    });
    }

};
