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
require_once("model/validate.php");
//require_once("/home/paulwood/db-globogym.php");  // paul woods

// Create an instance of the Base class
$f3 = Base::instance();     // i.e. Base f3 = new Base() in java


// Define a default route
$f3->route('GET /', function() {
    $view = new Template();
    echo $view->render('views/home.html');
});

// Login page
$f3->route('GET|POST /login', function($f3) {      // pass in f3 so is visible in function

    // var_dump($_POST);
    // can paste info once after using var-dump above

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_SESSION['username'] = $_POST['username'];
        $password = $_POST['password'];
        $valid = validateLogin($_SESSION['username'], $password);

        // Log user in
        if ($valid) {

            // redirect to account page
            $f3->reroute('account');
        } else {
            $f3->set('errors["login"]', 'You entered invalid login information, please try again');
        }

    }

    // Add meals to F3 hive
    //$f3->set('condiments', getCondiments());

    // instantiate a view
    $view = new Template();
    echo $view->render('views/login.html');
});



// Define Home page route
$f3->route('GET /home', function() {
    $view = new Template();
    echo $view->render('views/home.html');
});

// Define About Us page route
$f3->route('GET /about', function() {
    $view = new Template();
    echo $view->render('views/about.html');
});

// Define Memberships page route
$f3->route('GET /memberships', function() {
    $view = new Template();
    echo $view->render('views/memberships.html');
});

// Define an account page route
$f3->route('GET /account', function() {
    $view = new Template();
    echo $view->render('views/account.html');
});

// Run Fat-Free
$f3->run();                 // -> is the object operator, equiv to . in java