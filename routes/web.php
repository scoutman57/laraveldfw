<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@show');

/*
Route::get('/', function () {
  $data = [
    'online' => false,
    'locationname' => 'Lupe&rsquo;s TexMex Grill',
    'locationaddress' => '2200 Airport Freeway, Suite #505, Bedford, TX 76022',
    'locationphone' => '(817) 545-5004',
    'locationurl' => 'http://www.lupestxmx.com/',
    'locationlatitude' => 32.834653,
    'locationlongitude' => -97.132119,
    'datetime' => 'Thursday, May 5th at 7:00pm',
    'talk' => 'In-person MeetUp!',
    'speaker' => '',
    'speakerimg' => '',
    'speakerurl' => '',
    'additionalinfo' => '',
  ];

  return view('home', compact('data'));
});
*/

Route::get('/ask', ['as' => 'ask', function()
{
    return Redirect::away('https://www.twitter.com/laraveldfw');
}]);

Route::get('/live', array('as' => 'live', function()
{
    // Defaults to Laravel DFW Youtube channel if presentation URL is not present
    return Redirect::away(env('PRESENTATION_URL', 'https://www.youtube.com/channel/UCL5ERGPXuFrDPr96kIsWu7g'));
}));

Route::get('/rsvp', array('as' => 'rsvp', function()
{
    $meetup = \DB::table('meetups')
        ->where('status', 'upcoming')
        ->where('visibility', 'public')
        ->orderBy('start_time', 'asc')
        ->select('meetup_id')
        ->first();
    return Redirect::away('http://www.meetup.com/laravel-dallas-fort-worth/events/'.$meetup->meetup_id.'/');
}));

Route::get('/slack', 'HomeController@showSlackModal');

Route::get('/tell-us-about-you', ['as' => 'tellusaboutyou', function()
{
    return Redirect::away('https://docs.google.com/forms/d/1CVmWQdQEV91b5nPwlE4k2lmIyDKzjrhe0P0CTgjK2YA/viewform');
}]);

// Auth Stuff
Route::get('/logout', function()
{
    Auth::logout();
    return redirect('/');
});

Route::get('/login', 'LoginController@show');
Route::post('/loginAttempt', 'LoginController@attemptLogin');
Route::post('/authCheck', 'LoginController@checkAuth');
Route::post('/sendResetEmail', 'LoginController@sendResetEmail');

Route::get('/getEnv', function () {
    return response()->json([
        'env' => env('APP_ENV'),
    ]);
});


Route::group(['middleware' => ['auth']], function () {

    Route::get('/getAllUsers', 'LoginController@getAllUsers');

    Route::get('/dashboard', 'DashboardController@show');
    Route::get('/getAllMeetups', 'DashboardController@getAllMeetups');
    Route::post('/saveNewMeetup', 'DashboardController@saveNewMeetup');

    Route::get('/getAllMembers', 'DashboardController@getAllMembers');
});


Route::post('/requestSlackInvite', 'SlackController@requestInvite');
Route::get('/confirmSlackInvite/{token}', 'SlackController@confirmInvite');
Route::get('/test', 'DashboardController@test');

//SlackBot
Route::get('/login/slack', function(){
    return Socialite::with('slack')
        ->scopes(['bot'])
        ->redirect();
});
Route::get('/connect/slack', function(\GuzzleHttp\Client $httpClient){
    $response = $httpClient->post('https://slack.com/api/oauth.access', [
        'headers' => ['Accept' => 'application/json'],
        'form_params' => [
            'client_id' => env('SLACK_KEY'),
            'client_secret' => env('SLACK_SECRET'),
            'code' => $_GET['code'],
            'redirect_uri' => env('SLACK_REDIRECT_URI'),
        ]
    ]);
    $bot_token = json_decode($response->getBody())->bot->bot_access_token;
    echo "Your Bot Token is: ". $bot_token. " place it inside your .env as SLACK_TOKEN";
});
Route::post('/slack', 'SlackBotController@post');