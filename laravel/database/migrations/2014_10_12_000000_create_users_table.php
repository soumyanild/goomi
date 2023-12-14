<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('iso_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->tinyInteger('gender')->nullable()->comment("1=>male , 2=>female");
            $table->integer('notification')->default(1)->comment = '0 =>off, 1=>on';
            $table->integer('role')->default(2)->comments = '0 =>admin, 2=>user';
            $table->integer('status')->default(1)->comment = '0 =>inactive, 1=>active';
            $table->integer('type')->default(0)->comments("1 =>Pending, 2 =>Approve, 3=>rejected");
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('email_verification_otp')->nullable();
            $table->integer('device_type')->default(1)->comment = '0 =>IOS, 1=>Android';
            $table->text('fcm_token')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}