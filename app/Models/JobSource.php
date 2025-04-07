<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSource extends Model
{
    /** @use HasFactory<\Database\Factories\JobSourceFactory> */
    use HasFactory;

    protected $primaryKey = 'job_source_id';

    protected $fillable = [
        'source_name',
        'channel_or_group',
        'requires_third_party_login',
        'no_of_applications',
        'last_crawled',
        'no_of_jobs',
    ];

    protected $guarded = [
        'scrape_url',
    ];
}
