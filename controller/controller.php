<?php
// 328/globo_gym/controller/controller.php

class Controller
{
    private $_f3; //Fat-Free object

    function __construct($f3)
    {
        $this->_f3 = $f3;
    }

    function default(): void
    {
        session_destroy();      // TODO: Remove before production, testing purposes only. Means of manually destroying session.
        $view = new Template();
        echo $view->render('views/home.html');
    }

    function home(): void
    {
        $view = new Template();
        echo $view->render('views/home.html');
    }

    function account(): void
    {
        // Load account information
        // We have $_SESSION['username'] and $_SESSION['member_id']
        $result = DataLayer::loadMemberInformation($_SESSION['member_id']);

        if ($result) {

            $_SESSION['member_info'] = $result;

            // verify membership pricing is loaded
            if (!isset($_SESSION['membership_level']))
            {
                // addressed same as membership info, array of arrays
                // i.e. $_SESSION['membership_level']['level_name'], level_price_month, and level_price_year
                $_SESSION['membership_level'] = DataLayer::loadMembershipLevel($result['membership_level']);
            }

            // addCustomerMembership($memberID, $memberLevel)
            // TODO: Fix addition of fee to balance, prorate, and do not wipe out current balance
            if (isset($_POST['membership_level']))
            {
                if ($_POST['membership_level'] == 'Bronze')
                {
                    DataLayer::addCustomerMembership($_SESSION['member_id'], 1);
                }
                else if ($_POST['membership_level'] == 'Silver')
                {
                    DataLayer::addCustomerMembership($_SESSION['member_id'], 2);
                }
                else if ($_POST['membership_level'] == 'Gold')
                {
                    DataLayer::addCustomerMembership($_SESSION['member_id'], 3);
                }

                $this->_f3->reroute('account');
            }

        } else {
            // Invalid result, perhaps no member_id in session - reroute back to login page
            // Note, this is the only way I made the above fail, was to kill a logged in session
            $this->_f3->reroute('login');
        }

        $view = new Template();
        echo $view->render('views/account.html');
    }

    function join(): void
    {
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

            // if no errors, redirect to account page
            if (empty($this->_f3->get('errors'))) {

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
                if ($membership > 0){
                    $customer = new Member($membership);
                } else {
                    $customer = new User();
                }
                DataLayer::addCustomer($customer);

                $_SESSION['account_created'] = 1;

                // redirect to summary page
                $this->_f3->reroute('login');
            }
        }

        $view = new Template();
        echo $view->render('views/join.html');
    }

    function memberships(): void
    {
        // If the form has been posted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (Validate::validPackage($_POST['membership_level'])) {
                $_SESSION['package'] = $_POST['membership_level'];
                $this->_f3->reroute('join');
            }
        }
        $view = new Template();
        echo $view->render('views/memberships.html');
    }

    function aboutUs(): void
    {
        $view = new Template();
        echo $view->render('views/about.html');
    }

    function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            /*if (isset($_SESSION['member_info'])) {
                unset($_SESSION['member_info']);
            }

            if (isset($_SESSION['membership_level'])) {
                unset($_SESSION['membership_level']);
            }

            // Remove the welcome message after 1st creating an account if set
            if (isset($_SESSION['account_created'])) {
                unset($_SESSION['account_created']);
            }*/

            $_SESSION['username'] = $_POST['username'];

            $valid = DataLayer::validateLogin($_SESSION['username'], $_POST['password']);

            // Log user in
            if ($valid) {
                // echo "member_id = " . $valid . "<br>";
                // redirect to account page
                $_SESSION['member_id'] = $valid;
                $this->_f3->reroute('account');
            } else {
                $this->_f3->set('errors["login"]', 'You entered invalid login information, please try again');
            }

        }

        // Add meals to F3 hive
        //$f3->set('condiments', getCondiments());

        // instantiate a view
        $view = new Template();
        echo $view->render('views/login.html');
    }


    function dashboard(): void
    {
        $view = new Template();
        echo $view->render('views/admin_dashboard.html');
    }

    function members(): void
    {
        $view = new Template();
        echo $view->render('views/admin_members.html');
    }
}