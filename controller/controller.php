<?php
// 328/globo_gym/controller/controller.php

class Controller
{
    private $_f3; //Fat-Free object

    function __construct($f3)
    {
        $this->_f3 = $f3;
    }

    /**
     * Loads default view / home page
     * @return void
     */
    function default(): void
    {

        session_unset();
        session_destroy();      // TODO: Remove before production, testing purposes only. Means of manually destroying session.

        $view = new Template();
        echo $view->render('views/home.html');
    }

    /**
     * Loads home page (non-default)
     * @return void
     */
    function home(): void
    {
        $view = new Template();
        echo $view->render('views/home.html');
    }

    /**
     * Loads account page.  If member_info is not present
     * inside SESSION, redirects to the login page
     */
    function account(): void
    {
        // Load account information
        if (!isset($_SESSION['member_info'])) {
            $this->_f3->reroute('login');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['membership_level'])) {
                $account = new Member($_POST['membership_level']);
                $account->upgradeMember($_SESSION['member_info'], $_POST['membership_level']);
                $_SESSION['member_info'] = $account;
                $GLOBALS['dataLayer']->addCustomerMembership($_SESSION['member_info']);

                //$this->_f3->reroute('account');
            }
        }

        $view = new Template();
        echo $view->render('views/account.html');
    }

    /**
     * Loads and processes data from the join page
     * Calls data layer method to add user to database
     * @return void
     */
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
                    $customer = new Member($membership, $fname, $lname, $username, $password, $email, $phone, 0);
                } else {
                    $customer = new User($fname, $lname, $username, $password, $email, $phone, 0);
                }
                $GLOBALS['dataLayer']->addCustomer($customer);

                $_SESSION['account_created'] = 1;

                // redirect to summary page
                $this->_f3->reroute('login');
            }
        }

        $view = new Template();
        echo $view->render('views/join.html');
    }

    /**
     * Loads the memberships page.  If a POST is received,
     * saves that selected membership level to the SESSION
     * for use on the join page called immediately afterwards
     * @return void
     */
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

    /**
     * Displays the about us page
     * @return void
     */
    function aboutUs(): void
    {
        $view = new Template();
        echo $view->render('views/about.html');
    }

    /**
     * Presents and processes the login page.  Calls the
     * validateLogin() method from the DataLayer to process
     * the users login credentials.
     * @return void
     */
    function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $valid = $GLOBALS['dataLayer']->validateLogin($_POST['username'], $_POST['password']);

            // Log user in
            if ($valid) {
                $this->_f3->reroute('account');
            } else {
                $this->_f3->set('errors["login"]', 'You entered invalid login information, please try again');
            }

        }

        // instantiate a view
        $view = new Template();
        echo $view->render('views/login.html');
    }

    /**
     * Loads the admin dashboard
     * @return void
     */
    function dashboard(): void
    {
        $view = new Template();
        echo $view->render('views/admin_dashboard.html');
    }

    /**
     * Loads the admin members page
     * @return void
     */
    function members(): void
    {
        // Get the data from the model
        $members = $GLOBALS['dataLayer']->getMembers();
        $this->_f3->set('members',$members);

        // instantiate a view
        $view = new Template();
        echo $view->render('views/admin_members.html');
    }

    /**
     * This route changes the users password by taking in the current password,
     * the new password, and new password confirmation.  Returns an encoded
     * json string indicating success/failure w/ message.
     * @return string
     */
    function changePassword() : string
    {
        global $f3;

        // current_password
        $GLOBALS['dataLayer']->validPassword($_POST['new_password'],
            $_POST['new_password_confirm']);

        // If passwords are valid, update inside database
        if ($f3->get("errors['password']")) {
            $memberID = $_SESSION['member_info']->getMemberID();
            $oldPass = $_POST['current_password'];

            //function changePassword($memberID, $currentPassword, $newPassword)

            // if successful update ...
            if ($GLOBALS['dataLayer']->changePassword($memberID,
                $oldPass, $_POST['new_password'])) {
                return json_encode(array("status" => "updated", "error" => null));
            }
            // unsuccessful update, return update error
            else {
                return json_encode(array("status" => "error", "error" => 'database update error'));
            }

        }
        // if passwords are not valid, return error
        else {
            // password validation error in ---> $f3->get("errors['password']");
            return json_encode(array("status" => "invalid", "error" => $f3->get("errors['password']")));
        }

    }

}