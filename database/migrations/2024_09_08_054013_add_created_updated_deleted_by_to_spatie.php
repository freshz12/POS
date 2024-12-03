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
        // Update 'roles' table
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'created_by')) {
                $table->integer('created_by')->nullable();
            } else {
                $table->integer('created_by')->nullable()->change();
            }

            if (!Schema::hasColumn('roles', 'updated_by')) {
                $table->integer('updated_by')->nullable();
            } else {
                $table->integer('updated_by')->nullable()->change();
            }

            if (!Schema::hasColumn('roles', 'deleted_by')) {
                $table->integer('deleted_by')->nullable();
            } else {
                $table->integer('deleted_by')->nullable()->change();
            }

            if (!Schema::hasColumn('roles', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Update 'permissions' table
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'type')) {
                $table->string('type', 50);
            } else {
                $table->string('type', 50)->change();
            }

            if (!Schema::hasColumn('permissions', 'created_by')) {
                $table->integer('created_by');
            } else {
                $table->integer('created_by')->change();
            }

            if (!Schema::hasColumn('permissions', 'updated_by')) {
                $table->integer('updated_by');
            } else {
                $table->integer('updated_by')->change();
            }

            if (!Schema::hasColumn('permissions', 'deleted_by')) {
                $table->integer('deleted_by')->nullable();
            } else {
                $table->integer('deleted_by')->nullable()->change();
            }

            if (!Schema::hasColumn('permissions', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Update 'role_has_permissions' table
        Schema::table('role_has_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('role_has_permissions', 'created_by')) {
                $table->integer('created_by')->nullable();
            } else {
                $table->integer('created_by')->nullable()->change();
            }

            if (!Schema::hasColumn('role_has_permissions', 'updated_by')) {
                $table->integer('updated_by')->nullable();
            } else {
                $table->integer('updated_by')->nullable()->change();
            }

            if (!Schema::hasColumn('role_has_permissions', 'deleted_by')) {
                $table->integer('deleted_by')->nullable();
            } else {
                $table->integer('deleted_by')->nullable()->change();
            }

            if (!Schema::hasColumn('role_has_permissions', 'created_at')) {
                $table->timestamps();
            }

            if (!Schema::hasColumn('role_has_permissions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spatie', function (Blueprint $table) {
            //
        });
    }
};
