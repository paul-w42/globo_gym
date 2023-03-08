<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/../pdo-globogym.php');

/*
 * SQL Insert to add member as Admin
    insert into admin_permissions (member_id, edit_member_details, add_member, suspend_member, view_gym_memberships, view_member_details) values (4, 1,1,1,1,1);
 *
 * SQL to verify login and retrieve user info
   select m.member_id AS id, m.first_name AS fname, m.last_name AS lname, m.join_date AS joinDate,
    m.email AS email, m.phone AS phone, m.balance AS balance, m.membership_level AS memberLevel,
    admin_permissions.admin_id AS adminID, membership_levels.level_name AS levelName,
    membership_levels.level_price_month AS priceMonth, membership_levels.level_price_year
    AS priceYear, m.membership_pay_period AS payPeriod FROM members m LEFT JOIN admin_permissions
    ON admin_permissions.member_id = m.member_id LEFT JOIN membership_levels ON m.membership_level =
    membership_levels.membership_levels_id WHERE m.user_name = ? AND m.login_password = ?
 */


class DataLayer
{

    /*
     * For mysqli bind_param, type specification vars
     * i = int
     * d = float
     * s = string
     * b = blob
    */
    // Database connection object
    private $_dbh;

    function __construct()
    {
        try {
            // Instantiate a PDO object
            $this->_dbh = new PDO(DB_DRIVER, USERNAME, PASSWORD);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // converted to use pdo
    function addCustomer($customer)
    {
        $sql = "insert into members (first_name, last_name, user_name, login_password, join_date, email, phone, membership_level)
            values (:fName, :lName, :uName, :password, :joinDate, :email, :phone, :membershipLevel)";

        $password = sha1($customer->getPassword());

        $stmt = $this->_dbh->prepare($sql);
        $fName = $customer->getFName();
        $lName = $customer->getLName();
        $uName = $customer->getUName();
        $joinDate = $customer->getJoinDate();
        $email = $customer->getEmail();
        $phone = $customer->getPhone();
        $stmt->bindParam(':fName', $fName);
        $stmt->bindParam(':lName', $lName);
        $stmt->bindParam(':uName', $uName);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':joinDate', $joinDate);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);

        if ($customer instanceof Member)
        {
            $membershipLevel = $customer->getMembershipLevel();
        } else {
            $membershipLevel = -1;
        }
        $stmt->bindParam(':membershipLevel', $membershipLevel);

        $stmt->execute();

    }

    // TODO - TEST - Update to use PDO
    function addCustomerMembership($memberID, $memberLevel)
    {

        $sql = "update members set membership_level = ?, balance = " .
            "(select level_price_month from membership_levels where membership_levels_id = ?) where member_id = ?";

        // 2. prepare statement
        $stmt = $this->_dbh->prepare($sql);

        // 3. bind the parameters
        $stmt->bindValue(1, $memberLevel);
        $stmt->bindValue(2, $memberLevel);
        $stmt->bindValue(2, $memberID);
        //$memberLevel, $memberLevel, $memberID

        // 4. Execute the query
        $stmt->execute();


/*

        // 2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        $food = $orderObj->getFood();
        $meal = $orderObj->getMeal();
        $conds = $orderObj->getCondiments();

        // 3. Bind the parameters
        $statement->bindParam(':food', $food);
        $statement->bindParam(':meal', $meal);
        $statement->bindParam(':conds', $conds);

        // 4. Execute the query
        $statement->execute();

        // 5. Process the result
        $id = $this->_dbh->lastInsertId();
        return $id;
 */
    }

    /*
     * validateLogin(username, password),  validates the user login information against
     * the database.  Returns the members ID if valid, null otherwise.
     *
     */
    function validateLogin($username, $password)
    {

        $password = sha1($password);

        $sql = "select m.member_id AS id, m.first_name AS fname, m.last_name AS lname, " .
                "m.join_date AS joinDate, m.email AS email, m.phone AS phone, m.balance " .
                "AS balance, m.membership_level AS memberLevel, admin_permissions.admin_id " .
                "AS adminID, membership_levels.level_name AS levelName, " .
                "membership_levels.level_price_month " .
                "AS priceMonth, membership_levels.level_price_year AS priceYear, " .
                "m.membership_pay_period AS payPeriod FROM members m LEFT JOIN " .
                "admin_permissions ON admin_permissions.member_id = m.member_id LEFT JOIN " .
                "membership_levels ON m.membership_level = " .
                "membership_levels.membership_levels_id WHERE m.user_name = ? AND " .
                "m.login_password = ?";

        // Just added priceMonth, priceYear, payPeriod,

        $stmt = $this->_dbh->prepare($sql);
        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, $password);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $msg = $username . ', ';

        // we have a row returned
        if (isset($result['id'])) {

            $account;
            /*
             * These two statements will wreck the page, BUT they will display the full SQL query - useful
            $stmt->debugDumpParams();
            ob_end_flush();
            */


            $msg = $msg . '<br>';

            // admin account
            //if (isset($result['adminID'])) {
            if ($result['adminID'] != null) {

                echo 'processed as an admin<br>';
                $account = new Admin($result['adminID']);
                $msg = $msg . 'ADMIN, ';
            }
            // paying member
            else if ($result['memberLevel'] != -1) {
                $account = new Member($result['memberLevel']);
                $account->setMembershipLevelName($result['levelName']);
                $account->setMembershipPriceMonth($result['priceMonth']);
                $account->setMembershipPriceYear($result['priceYear']);
                $account->setMembershipPayPeriod($result['payPeriod']);
                $account->setMembershipLevel($result['memberLevel']);
                $msg = $msg . 'Paying Member, ';
            }
            // non-subscribing member
            else {
                $msg = $msg . 'Non-Paying Member, ';
                $account = new User();
            }

            echo 'setting user values now.<br>';

            $account->setMemberID($result['id']);
            $account->setFName($result['fname']);
            $account->setLName($result['lname']);
            $account->setJoinDate($result['joinDate']);
            $account->setEmail($result['email']);
            $account->setPhone($result['phone']);
            $account->setBalance($result['balance']);
            $account->setUName($username);
            $account->setPassword($password);

            echo 'finished setting user values.<br>';

            $_SESSION['member_info'] = $account;

            $_SESSION['debug_msg'] = $msg;



            //ob_end_flush();       // force echo output, does crash page though

            return true;
        }


        return false;
    }

    /*
     * Loads user information from the database when visiting the account page.
     * Returns the result/row back to the calling function, packaged as an associative array.
     * i.e. $firstName = $row['first_name'];
     */
    /*
    function loadMemberInformation($memberID)
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
*/
    /*
    function loadMembershipLevel($membershipID)
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
*/

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