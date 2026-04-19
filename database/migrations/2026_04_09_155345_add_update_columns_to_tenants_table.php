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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('current_version')->nullable()->after('logo_url');
            $table->timestamp('last_updated_at')->nullable()->after('current_version');
            $table->timestamp('update_dismissed_at')->nullable()->after('last_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['current_version', 'last_updated_at', 'update_dismissed_at']);
        });
    }
};
