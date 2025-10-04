<?php 
//---------------Model--------------------------------------------Menuuser.php

//----------------------------------------------
//FILE NAME:  Menuuser.php gen for Servit Framework Model
//Created by: Tlen<limweb@hotmail.com>
//DATE: 12-12-2022 16:30:35

//----------------------------------------------
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use DB;

class Menuuser extends BaseModel { 

        protected	$table="menuuser"; 
        protected	$primaryKey="user_name";

        protected	$dateFormat = 'Y-m-d H:i:s';
        public	        $timestamps = false;
        
        protected	$guarded = [];
        protected	$fillable = ["user_name","b_Password","a_Password","id_random","program_no","departmentcode","department","faction","user_pict","user_level","emp_id","nic_name","name_thai","name_eng","display_name","position_level","positionname","user_equal","master_id","birthday","user_start","USER_END","passwordExpires","userchange","usenupdate",];
        protected	$hidden = ["b_Password","a_Password",];
        protected	$appends = [];
        protected	$with = [];
        protected	$dates = [];
        protected       $casts = [ ];
} 

