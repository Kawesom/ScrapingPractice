<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundJobs extends Model
{
    /** @use HasFactory<\Database\Factories\FoundJobsFactory> */
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_name',
        'source_id',
        'company_name',
        'industry',
        'employer_image',
        'salary',
        'description',
        'requirements',
        'gender_preference',
        'people_applied',
        'years_of_experience',
        'worker_looking_for',
        'min_age',
        'max_age',
        'state',
        'country',
        'field_hiring_for',
        'tags',
        'job_type',
        'relevant_fields',
        'source',
        'email_to_apply',
        'link_to_apply',
        'date_posted',
        'deadline',
        'job_url',
        'city_or_province',
        'qualification',
        'materials_to_apply',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
       //
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'json',
            'relevant_fields' => 'json',
            'date_posted' => 'datetime',
            'deadline' => 'datetime',
            'materials_to_apply' => 'array',
        ];
    }
}
