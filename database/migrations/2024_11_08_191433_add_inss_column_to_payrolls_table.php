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
    Schema::table('payrolls', function (Blueprint $table) {
        $table->decimal('inss', 10, 2)->default(0)->after('salary_amount');
    });
}

public function down()
{
    Schema::table('payrolls', function (Blueprint $table) {
        $table->dropColumn('inss');
    });
}

};
