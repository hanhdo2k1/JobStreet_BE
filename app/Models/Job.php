<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "jobs";
    use HasFactory;
    protected $fillable=[
        "company_id",
        "cat_id",
        "position",
        "salary",
        "type",
        "description",
        "status",
        "close_day"
     ];
     public function company()
     {
         return $this->belongsTo(Company::class);
     }

     public function categories()
     {
         return $this->belongsTo(Categories::class);
     }

     public function application()
     {
         return $this->hasMany(Application::class);
 }
}
