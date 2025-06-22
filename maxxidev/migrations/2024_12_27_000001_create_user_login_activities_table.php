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
        Schema::create('user_login_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('last_ip')->nullable()->comment('Last login IP address');
            $table->date('curdate')->nullable()->comment('Current date');
            $table->decimal('lat', 10, 8)->nullable()->comment('Latitude');
            $table->decimal('long', 11, 8)->nullable()->comment('Longitude');
            $table->string('user_agent')->nullable()->comment('User agent');
            $table->string('device')->nullable()->comment('Device type');
            $table->string('browser')->nullable()->comment('Browser');
            $table->string('platform')->nullable()->comment('Operating system');
            $table->timestamp('last_activity')->nullable()->comment('Last activity time');
            $table->timestamps();
            
            $table->index(['last_ip', 'curdate']);
            $table->index('user_id');
            $table->index('curdate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_activities');
    }
}; 