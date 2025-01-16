<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table( 'markers', function ( Blueprint $table ) {
            $table->foreignId('present_id')
                ->nullable()
                ->constrained('presents')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        } );
        Schema::table( 'presents', function ( Blueprint $table ) {
            $table->string('img')->nullable();
            $table->foreignId('marker_id')
                ->nullable()
                ->constrained('markers')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        } );
    }

    public function down()
    {
        Schema::table( 'markers', function ( Blueprint $table ) {
            $table->dropForeign('markers_present_id_foreign');
            $table->dropColumn( array(
                'present_id'
            ) );
        } );
        Schema::table( 'presents', function ( Blueprint $table ) {
            $table->dropForeign('presents_marker_id_foreign');
            $table->dropColumn( array(
                'img',
                'marker_id'
            ) );
        } );
    }
};
