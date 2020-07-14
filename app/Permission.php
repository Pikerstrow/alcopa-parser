<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Permission as VoyagerPermission;

class Permission extends VoyagerPermission
{
    public static function generateFor($table_name, array $permissions = [])
    {
        if (!empty($permissions)) {
            foreach($permissions as $permission){
                static::firstOrCreate(['key' => $permission . '_' . $table_name, 'table_name' => $table_name]);
            }
        } else {
            parent::generateFor($table_name);
        }
    }
}
