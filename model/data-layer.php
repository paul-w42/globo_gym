<?php

require '/home/paulwood/db-globogym.php';

class DataLayer
{

    /*
     * For mysqli bind_param, type specification vars
     * i = int
     * d = float
     * s = string
     * b = blob
    */
    static function addCustomer($fname, $lname, $password, $email, $phone, $username, $membership)
    {

        global $cnxn;

//        if ($membership == null) {
//            $membership = -1;
//        }

        $sql = "insert into members (first_name, last_name, user_name, login_password, join_date, email, phone, membership_level) 
            values (?, ?, ?, ?, ?, ?, ?, ?)";

        $password = sha1($password);
        $joinDate = date('Y-m-d'); // https://www.php.net/manual/en/function.date.php#85692

        $stmt = $cnxn->prepare($sql);
        $stmt->bind_param("sssssssi", $fname, $lname, $username, $password, $joinDate, $email, $phone, $membership);

        $stmt->execute();

        $returnID = 0;

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $returnID = mysqli_insert_id($cnxn);
        }

        //mysqli_stmt_close($stmt);
        //mysqli_close($cnxn);

        //echo "AddCustomer(), Returning New CustomerID: " . $returnID . "<br>\n";

        return $returnID;
    }

    static function addCustomerMembership($memberID, $memberLevel)
    {

        global $cnxn;

        $sql = "update members set membership_level = ?, balance = " .
            "(select level_price_month from membership_levels where membership_levels_id = ?) where member_id = ?";

        $stmt = $cnxn->prepare($sql);

        $stmt->bind_param("iii", $memberLevel, $memberLevel, $memberID);

        $stmt->execute();
    }

    /*
     * validateLogin(username, password),  validates the user login information against
     * the database.  Returns the members ID if valid, null otherwise.
     *
     * Uses bind_result(...)
     */
    static function validateLogin($username, $password)
    {

        global $cnxn;

        $password = sha1($password);

        $sql = "SELECT member_id FROM members WHERE user_name = ? AND login_password = ?";

        $stmt = $cnxn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);   // can change values and re-do this line x times

        $stmt->execute();
        //$member_id = 0;
        $stmt->bind_result($col1);  // $col1 is declared here as a reference, no need to declare earlier
        $stmt->fetch();
        return $col1;
    }

    /*
     * Loads user information from the database when visiting the account page.
     * Returns the result/row back to the calling function, packaged as an associative array.
     * i.e. $firstName = $row['first_name'];
     */
    static function loadMemberInformation($memberID)
    {

        global $cnxn;

        $memberID = intval($memberID);

        $sql = "SELECT first_name, last_name, join_date, email, phone, balance, membership_level FROM members WHERE member_id = ?";

        $stmt = $cnxn->prepare($sql);

        $stmt->bind_param("i", $memberID);   // can change values and re-do this line x times

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();

        // $firstName = $row['first_name'];
    }

    static function loadMembershipLevel($membershipID)
    {
        global $cnxn;

        $sql = "SELECT level_name, level_price_month, level_price_year FROM membership_levels 
                       WHERE membership_levels_id = ?";

        $stmt = $cnxn->prepare($sql);

        $stmt->bind_param("i", $membershipID);   // can change values and re-do this line x times

        $stmt->execute();
        $result = $stmt->get_result();
        //return $result;     // cycle through each row to gather required info
        // https://www.php.net/manual/en/mysqli-result.fetch-assoc.php
        return $result->fetch_assoc();
    }


    /*
     * Example function from prior project using prepared statement
     */
    /*
        function customerExists($fname, $email, $phone) {

            global $cnxn;

            $sql = "SELECT max(customer_id) AS customer_id FROM customers WHERE
                    first_name=? AND (email=? OR phone=?)";

            $stmt = $cnxn->prepare($sql);
            $stmt->bind_param("sss", $fname, $email, $phone);   // can change values and re-do this line n times

            $stmt->execute();

            $customerID = 0;

            $stmt->bind_result($customer_id);

            if ($stmt->fetch()) {
                $customerID = $customer_id;
            }

            //echo "customerExists(), returning customerID: " . $customerID . "<br>\n";

            return $customerID;
        }
    */
}