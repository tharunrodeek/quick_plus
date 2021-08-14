<?php

namespace App;
use App\Scopes\EmployeeScope;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $table = '0_users';


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmployeeScope);
    }

}
