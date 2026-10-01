<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\Event;
use App\Models\Information;
use App\Models\Review;
use App\Models\Schedule;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $bannerInit = Information::where('code', 'banner-init')->first();
        $about = About::where('number', 1)->first();
        $schedules = Schedule::where('active', 1)->orderBy('order', 'ASC')->get();
        $category = Category::with('menus')->where('order', 1)->first();
        $characteristics = Characteristic::where('active', 1)->orderBy('order', 'ASC')->get();
        $events = Event::where('active', 1)->orderBy('order', 'ASC')->get();
        $reviews = Review::get();

        return view('content.home', compact('bannerInit', 'about', 'schedules', 'category', 'characteristics', 'events', 'reviews'));
    }
}
