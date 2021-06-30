<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenericlearningactivityFkToActivityreflectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_reflections', function (Blueprint $table) {
            $table->foreign('genericlearningactivity_id', 'fk_Activity_Reflections_GenericLearningActivity1')->references('gla_id')->on('genericlearningactivity')->onUpdate('NO ACTION')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_reflections', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign('FK_ACTIVITY_REFLECTIONS_GENERICLEARNINGACTIVITY1');
            }
        });
    }
}
