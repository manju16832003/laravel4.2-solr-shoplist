<?php

class HomeController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */

    public function showWelcome()
    {
        $solr = new Solr();
        $shops = $solr->getAll();
        $facets = $shops['facets'];
        $results = $shops['result'];

        $category = Category::all();
        $states = State::all();
        $towns = Town::all();
        return View::make('index', compact('shops', 'facets', 'results', 'category', 'states', 'towns'));
    }

    public function showAboutUs()
    {
        return View::make('aboutus');
    }

    public function showContactUs()
    {
        return View::make('contactus');
    }
}
