<?php

//----------------------------------------------
//FILE NAME:  BaseModel.php gen for Servit Framework Model 
//Created by: Tlen<limweb@hotmail.com> 
//DATE: 2021-08-10(Tue)  01:48:11  

//----------------------------------------------


use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes; 
//use DB; 
 
class BaseModel extends Model 
{ 
    protected static function KeyName() {
        return (new static)->getKeyName();
    }

    protected static function TableName() {
        return (new static)->getTable();
    }

}