<?php
// Paul Woods, Jasmine David, Stewart Lovell
//
// Controller for the GloboGym project

// Turn on error reporting (probably want off when live)
// this will turn it on for each page in our project
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Require the autoload file
require_once("vendor/autoload.php");
require_once("/home/paulwood/db-globogym.php");  // paul woods

// Create an instance of the Base class
$f3 = Base::instance();     // i.e. Base f3 = new Base() in java

// Instantiate a controller object
$con = new Controller($f3);
$dataLayer = new DataLayer();

// Define a default route
$f3->route('GET /', function () {
    $GLOBALS['con']->default();
});

// Login page
$f3->route('GET|POST /login', function ($f3) {      // pass in f3 so is visible in function
    $GLOBALS['con']->login();
});


// Define Home page route
$f3->route('GET /home', function () {
    $GLOBALS['con']->home();
});

// Define About Us page route
$f3->route('GET /about', function () {
    $GLOBALS['con']->aboutUs();
});

// Define Memberships page route
$f3->route('GET|POST /memberships', function ($f3) {
    $GLOBALS['con']->memberships();
});

// Define a join page route
$f3->route('GET|POST /join', function ($f3) {
    $GLOBALS['con']->join();
});

// Define an account page route
$f3->route('GET|POST /account', function ($f3) {
    $GLOBALS['con']->account();
});

// Define Admin - dashboard page route
$f3->route('GET /admin_dashboard', function ($f3) {
    $GLOBALS['con']->dashboard();
});

// Define Admin - member page route
$f3->route('GET /admin_members', function ($f3) {
    $GLOBALS['con']->members();
});

// Run Fat-Free
$f3->run();                 // -> is the object operator, equiv to . in java