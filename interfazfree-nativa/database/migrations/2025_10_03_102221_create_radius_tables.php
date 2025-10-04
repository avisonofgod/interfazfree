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
        Schema::create('radcheck', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->index();
            $table->string('attribute', 64);
            $table->string('op', 2)->default('==');
            $table->string('value', 253);
            $table->timestamps();
        });

        Schema::create('radreply', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->index();
            $table->string('attribute', 64);
            $table->string('op', 2)->default('=');
            $table->string('value', 253);
            $table->timestamps();
        });

        Schema::create('radacct', function (Blueprint $table) {
            $table->id('radacctid');
            $table->string('acctsessionid', 64)->index();
            $table->string('acctuniqueid', 32)->unique();
            $table->string('username', 64)->index();
            $table->string('realm', 64)->nullable();
            $table->string('nasipaddress', 15)->index();
            $table->string('nasportid', 32)->nullable();
            $table->string('nasporttype', 32)->nullable();
            $table->timestamp('acctstarttime')->nullable()->index();
            $table->timestamp('acctupdatetime')->nullable();
            $table->timestamp('acctstoptime')->nullable();
            $table->integer('acctsessiontime')->unsigned()->nullable();
            $table->string('acctauthentic', 32)->nullable();
            $table->string('connectinfo_start', 50)->nullable();
            $table->string('connectinfo_stop', 50)->nullable();
            $table->bigInteger('acctinputoctets')->unsigned()->nullable();
            $table->bigInteger('acctoutputoctets')->unsigned()->nullable();
            $table->string('calledstationid', 50)->nullable();
            $table->string('callingstationid', 50)->nullable();
            $table->string('acctterminatecause', 32)->nullable();
            $table->string('servicetype', 32)->nullable();
            $table->string('framedprotocol', 32)->nullable();
            $table->string('framedipaddress', 15)->nullable();
            $table->timestamps();
            
            $table->index(['username', 'acctstarttime']);
            $table->index(['acctstoptime']);
        });

        Schema::create('radpostauth', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64);
            $table->string('pass', 64);
            $table->string('reply', 32);
            $table->timestamp('authdate')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radpostauth');
        Schema::dropIfExists('radacct');
        Schema::dropIfExists('radreply');
        Schema::dropIfExists('radcheck');
    }
};
