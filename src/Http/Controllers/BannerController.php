<?php

namespace Jeffpereira\RealEstate\Http\Controllers;

use Illuminate\View\View;

class BannerController extends Controller
{
    public function list(): View
    {
        return view('jprealestate::banners.list');
    }
}
