<?php
// Paul Woods, Jasmine David, Stewart Lovell
//
// Controller for the GloboGym project

// Turn on error reporting (probably want off when live)
// this will turn it on for each page in our project
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Require the autoload file
require_once("vendor/autoload.php");

// Create an instance of the Base class
$f3 = Base::instance();     // i.e. Base f3 = new Base() in java


// Define a default route
$f3->route('GET /', function() {
    $view = new Template();
    echo $view->render('views/home.html');
});

// Run Fat-Free
$f3->run();                 // -> is the object operator, equiv to . in java

