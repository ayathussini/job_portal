<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    public function jobType(){
        return $this->belongsTo(JobType::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function applications(){

        return $this->hasmany(JobApplication::class);

    }
    public function user(){
        return $this->belongsTo(Category::class);
    }
    protected $fillable = [
    'title', 'category_id', 'job_type_id', 'vacancy', 'salary', 'location', 
    'description', 'qualifications', 'experience', 'keywords', 'company_name',
    'company_location', 'website','benefits','responsabilites'
];

}
