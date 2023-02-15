<?php

function validateLogin($user, $pass)
{
    if ($user == 'jdoe' && sha1($pass) == '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8') {
        return true;
    }
    return false;
}

function validPackage($package)
{
    return (in_array($package, array("bronze", "silver", "gold")));
}

function validPhone($phone)
{
    // retain just valid digits, will test only those.  assume below are benign chars
    $phone = str_replace('-', '', $phone);                // remove - signs
    $phone = str_replace('(', '', $phone);                // remove ( signs
    $phone = str_replace(')', '', $phone);                // remove ) signs
    $phone = str_replace(' ', '', $phone);                // remove spaces

    // test that remaining string is a valid integer after removing benign chars
    if (!filter_var($phone, FILTER_VALIDATE_INT)) {
        return false;
    }

    // test for strlen of either 11 (1-206-555-1212), 10 (206-555-1212), or 7 (555-1212)
    $length = strlen($phone);

    if (($length == 7) || ($length == 10) || ($length == 11)) {
        return true;
    } else {
        return false;
    }
}