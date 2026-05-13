<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate data from ict_maintenance to ict_maintenance_inspection
        if (Schema::hasTable('ict_maintenance')) {
            $maintenanceRecords = DB::table('ict_maintenance')->get();
            foreach ($maintenanceRecords as $record) {
                DB::table('ict_maintenance_inspection')->insertOrIgnore([
                    'request_id' => $record->request_id,
                    'type' => 'maintenance',
                    'date_current' => $record->date_current,
                    'time_current' => $record->time_current,
                    'req_name' => $record->req_name,
                    'req_designation' => $record->req_designation,
                    'req_DO' => $record->req_DO,
                    'DOPE' => $record->DOPE,
                    'brand' => $record->brand,
                    'prop_no' => $record->prop_no,
                    'serial_no' => $record->serial_no,
                    'date_last_repair' => $record->date_last_repair,
                    'defects' => $record->defects,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            }
        }

        // Migrate data from ict_equipment_inspection to ict_maintenance_inspection
        if (Schema::hasTable('ict_equipment_inspection')) {
            $inspectionRecords = DB::table('ict_equipment_inspection')->get();
            foreach ($inspectionRecords as $record) {
                DB::table('ict_maintenance_inspection')->insertOrIgnore([
                    'request_id' => $record->request_id,
                    'type' => 'inspection',
                    'item' => $record->item,
                    'property_no' => $record->property_no,
                    'receipt_no' => $record->receipt_no,
                    'acquisition_cost' => $record->acquisition_cost,
                    'acquisition_date' => $record->acquisition_date,
                    'complaints' => $record->complaints,
                    'scope_last_repair' => $record->scope_last_repair,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            }
        }

        // Update request_type_table from old types to new merged type
        DB::table('requests')->where('request_type_table', 'ict_maintenance')->update(['request_type_table' => 'ict_maintenance_inspection']);
        DB::table('requests')->where('request_type_table', 'ict_equipment_inspection')->update(['request_type_table' => 'ict_maintenance_inspection']);
    }

    public function down(): void
    {
        // Rollback: reverse the request_type_table updates
        DB::table('requests')->where('request_type_table', 'ict_maintenance_inspection')->delete();
    }
};
