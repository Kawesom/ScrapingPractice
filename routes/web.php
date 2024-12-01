<?php

use App\Spiders\FirstSpider;
use Illuminate\Support\Facades\Route;
use RoachPHP\Roach;

Route::get('/', function () {
    //return view('welcome');

    //dd(Roach::collectSpider(FirstSpider::class));
});
