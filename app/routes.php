<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
  $data = [
    'hidemap' => true,
    'locationname' => "Google Hangouts",
    'locationurl' => 'https://plus.google.com/events/cb7dpbs7tglv3d4u5jj2o5sec2o',
    'datetime' => 'Thursday, February 5th at 7:00pm',
    'speaker' => 'Laravel DFW',
    'speakerurl' => 'https://twitter.com/laraveldfw',
    'speakerimg' => 'img/laravel-dfw-image.jpg',
    'talk' => 'Laravel 5 Round Table'
  ];

  return View::make('home', compact('data'));
});

Route::get('/ask', ['as' => 'ask', function()
{
  return Redirect::away('https://tannerhearne.typeform.com/to/zZGZJF');
}]);

Route::get('/live', array('as' => 'live', function()
{
  return Redirect::away('https://plus.google.com/events/cb7dpbs7tglv3d4u5jj2o5sec2o');
}));


Route::get('/rsvp', array('as' => 'rsvp', function()
{
  return Redirect::away('http://www.meetup.com/laravel-dallas-fort-worth/events/219984400/');
}));