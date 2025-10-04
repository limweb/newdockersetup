<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

require_once __DIR__ . "/Menuuser.php";

class Login extends Menuuser
{
        protected        $table = "menuuser";
        protected        $with = [];
        protected        $hidden = ["b_Password","a_Password","created_at", "updated_at", "deleted_at"];
        protected        $casts = [ 
          'user_name'=>'string',           
          "id_random" => 'string',
          "program_no" => 'string',
          "departmentcode" => 'string',
          "department" => 'string',
          "faction" => 'string',
          "user_pict" => 'string',
          "user_level" => 'string',
          "emp_id" => 'string',
          "nic_name" => 'string',
          "name_thai" => 'string',
          "name_eng" => 'string',
          "display_name" => 'string',
          "position_level" => 'string',
          "positionname" => 'string',
          "user_equal" => 'string',
          "master_id" => 'string',
          "birthday" => 'string',
          "userchange" => 'string',
          "usenupdate" => 'string',
        ];
                //integer, real, float, double, string, boolean, object, array, collection, date and datetime. 
            //    restcast snippet 
            //    'created_at' => 'datetime:Y-m-d',
}
