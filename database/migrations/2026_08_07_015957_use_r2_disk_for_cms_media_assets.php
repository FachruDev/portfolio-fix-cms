<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('cms_media_assets')
            ->where('disk', 'public')
            ->update(['disk' => 'r2']);
    }

    public function down(): void
    {
        DB::table('cms_media_assets')
            ->where('disk', 'r2')
            ->update(['disk' => 'public']);
    }
};
