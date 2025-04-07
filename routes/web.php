<?php

use App\Class\GetUserAgent;
use App\Models\FoundJobs;
use App\Spiders\FirstSpider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use RoachPHP\Roach;

Route::get('/', function () {
    //return view('welcome',[
    //    'f' => FoundJobs::find(10),
    //]);
    dd((new GetUserAgent())->Rand());
    dd($f->job_name, $f->company_name, $f->job_url, $f->date_posted->toDateTimeString());
    $first = FoundJobs::find(16);
    $second = FoundJobs::latest('id')->first();//FoundJobs::find(105);
    dd($first->job_name === $second->job_name && $first->company_name === $second->company_name && $first->job_url === $second->job_url);
    //dd(Roach::collectSpider(FirstSpider::class));
});
