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
require_once("model/validate.php");
require_once("model/data-layer.php");
require_once("/home/paulwood/db-globogym.php");  // paul woods

// Create an instance of the Base class
$f3 = Base::instance();     // i.e. Base f3 = new Base() in java


// Define a default route
$f3->route('GET /', function () {
    session_destroy();      // TODO: Remove before production, testing purposes only. Means of manually destroying session.
    $view = new Template();
    echo $view->render('views/home.html');
});

// Login page
$f3->route('GET|POST /login', function ($f3) {      // pass in f3 so is visible in function

    // var_dump($_POST);
    // can paste info once after using var-dump above

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_SESSION['member_info'])) {
            unset($_SESSION['member_info']);
        }

        if (isset($_SESSION['membership_level'])) {
            unset($_SESSION['membership_level']);
        }

        // Remove the welcome message after 1st creating an account if set
        if (isset($_SESSION['account_created'])) {
            unset($_SESSION['account_created']);
        }

        $_SESSION['username'] = $_POST['username'];

        $valid = validateLogin($_SESSION['username'], $_POST['password']);

        // Log user in
        if ($valid) {
            echo "member_id = " . $valid . "<br>";
            // redirect to account page
            $_SESSION['member_id'] = $valid;
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
$f3->route('GET /home', function () {
    $view = new Template();
    echo $view->render('views/home.html');
});

// Define About Us page route
$f3->route('GET /about', function () {
    $view = new Template();
    echo $view->render('views/about.html');
});

// Define Memberships page route
$f3->route('GET|POST /memberships', function ($f3) {

    // If the form has been posted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (Validate::validPackage($_POST['membership_level'])) {
            $_SESSION['package'] = $_POST['membership_level'];
            $f3->reroute('join');
        }
    }
    $view = new Template();
    echo $view->render('views/memberships.html');
});

// Define a join page route
$f3->route('GET|POST /join', function ($f3) {

    //echo 'PHP Version ' . phpversion() . '<br>';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $fname = $_POST['first'];
        $lname = $_POST['last'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        Validate::validName($fname, "first", "First Name");
        Validate::validName($lname, "last", "Last Name");
        Validate::validUsername($username);
        Validate::validEmail($email);
        Validate::validPhone($phone);
        Validate::validPassword($password, $password2);

        // TODO: Test for existing customer username / email
        // 1st test password, 'password1'

        // if no errors, redirect to mailing_lists page
        if (empty($f3->get('errors'))) {

            $membership = null;
            // add customer package information if set ...
            if (isset($_SESSION['package'])) {
                $membership = $_SESSION['package'];
            }

            if ($membership == 'bronze') {
                $membership = 1;
            }
            else if ($membership == 'silver') {
                $membership = 2;
            }
            else if ($membership == 'gold') {
                $membership = 3;
            }

            // save data to database
            addCustomer($fname, $lname, $password, $email, $phone, $username, $membership);

            $_SESSION['account_created'] = 1;

            // redirect to summary page
            $f3->reroute('login');
        }
    }

    $view = new Template();
    echo $view->render('views/join.html');

});

// Define an account page route
$f3->route('GET /account', function ($f3) {

    // Load account information
    // We have $_SESSION['username'] and $_SESSION['member_id']
    $result = loadMemberInformation($_SESSION['member_id']);

    if ($result) {

        $_SESSION['member_info'] = $result;

        // verify membership pricing is loaded
        if (!isset($_SESSION['membership_level'])) {
            // addressed same as membership info, array of arrays
            // i.e. $_SESSION['membership_level']['level_name'], level_price_month, and level_price_year
            $_SESSION['membership_level'] = loadMembershipLevel($result['membership_level']);
        }



    } else {
        // Invalid result, perhaps no member_id in session - reroute back to login page
        // Note, this is the only way I made the above fail, was to kill a logged in session
        $f3->reroute('login');
    }

    $view = new Template();
    echo $view->render('views/account.html');
});

// Run Fat-Free
$f3->run();                 // -> is the object operator, equiv to . in java