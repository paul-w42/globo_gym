<?php

class Validate
{

    /**
     * Checks for a valid name, can be used for any field that checks
     * for a minimum length of 3 chars.
     * @param $name
     * @param $fieldName
     * @param $fullFieldName
     * @return void
     */
    static function validName($name, $fieldName, $fullFieldName) {

        global $f3;

        if (!ctype_alpha($name) || strlen($name) < 3) {
            //return false;
            $f3->set("errors[$fieldName]", "A valid $fullFieldName is required.");
        }
        else {
            $_SESSION[$fieldName] = $name;
        }
        //return true;
    }

    /**
     * Checks for a valid username.  Minimum size is 2 characters
     * @param $name
     * @return void
     */
    static function validUsername($name) {

        global $f3;

        if (strlen($name) < 2) {
            //return false;
            $f3->set("errors['username']", "A valid username is required.");
        }
        else {
            $_SESSION['username'] = $name;
        }
        //return true;
    }

    /**
     * Validates the entered email address
     * @param $email
     * @return void
     */
    static function validEmail($email) {

        global $f3;

        // FILTER_VALIDATE_EMAIL
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $f3->set("errors['email']", "A valid email address is required.");
        }
        else {
            $_SESSION['email'] = $email;
        }
    }

    /**
     * Verifies that a valid package was selected/submitted.
     * @param $package
     * @return bool
     */
    static function validPackage($package)
    {
        return (in_array($package, array("bronze", "silver", "gold")));
    }

    /**
     * Check for a valid phone number.  Validates north american numbers only.
     * @param $phone
     * @return void
     */
    static function validPhone($phone)
    {

        global $f3;

        // retain just valid digits, will test only those.  assume below are benign chars
        $phone = str_replace('-', '', $phone);                // remove - signs
        $phone = str_replace('(', '', $phone);                // remove ( signs
        $phone = str_replace(')', '', $phone);                // remove ) signs
        $phone = str_replace(' ', '', $phone);                // remove spaces

        // test that remaining string is a valid integer after removing benign chars
        if (!filter_var($phone, FILTER_VALIDATE_INT)) {
            $f3->set("errors['phone']", "A valid phone number is required.");
        }

        // test for strlen of either 11 (1-206-555-1212), 10 (206-555-1212), or 7 (555-1212)
        $length = strlen($phone);

        if (!(($length == 7) || ($length == 10) || ($length == 11))) {
            $f3->set("errors['phone']", "A valid phone number is required.  Incorrect length.");
        }

        if ($length == 11) {
            $sArray = str_split($phone);
            if ($sArray[0] != '1') {
                $f3->set("errors['phone']", "A valid phone number is required, Country Code must be '1'");
            }
        }

        $_SESSION['phone'] = $phone;
    }


    /**
     * Checks that the entered passwords are valid, i.e. correct length,
     * and that they both match each other.  Return appropriate errors
     * if either test fails.
     * @param $password
     * @param $password2
     * @return void
     */
    static function validPassword($password, $password2) {

        global $f3;

        if ($password != $password2) {
            $f3->set("errors['password']", "Entered passwords do not match.");
            return;
        }

        if (strlen($password) < 9) {
            $f3->set("errors['password']", "Password must be at least 9 characters.");
        }

    }
}