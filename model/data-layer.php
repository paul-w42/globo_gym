<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/../pdo-globogym.php');
//require ($_SERVER['DOCUMENT_ROOT'].'/../pdo-config.php'); // Jasmine's pdo

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

    /**
     * Update the membership level inside the members table.  Also as a result
     * udpates the members balance owed - charges for the 1st pay period
     * @param $memberID
     * @param $memberLevel
     * @return void
     */
    function addCustomerMembership($member)
    {

        $sql = "update members set membership_level = ?, balance = " .
            "(select level_price_month from membership_levels where membership_levels_id = ?) where member_id = ?";

        $stmt = $this->_dbh->prepare($sql);

        //$stmt->bind_param("iii", $memberLevel, $memberLevel, $memberID);
        $stmt->bindValue(1, $member->getMembershipLevel());
        $stmt->bindValue(2, $member->getMembershipLevel());
        $stmt->bindValue(3, $member->getMemberID());

        $stmt->execute();

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


    /**
     * This function takes a member_id, current_password, and new_password,
     * and updates the member table with that new password for that
     * member_id if the old_password is correct.
     *
     * @param $memberID
     * @param $currentPassword
     * @param $newPassword
     * @return bool
     */
    function changePassword($memberID, $currentPassword, $newPassword)
    {

        $newPassword = sha1($newPassword);
        $currentPassword = sha1($currentPassword);

        $sql = "UPDATE members SET login_password = :newPass WHERE " .
            "member_id = :memberID AND login_password = :oldPass";


        $stmt = $this->_dbh->prepare($sql);

        $stmt->bindParam(':newPass', $newPassword);
        $stmt->bindParam(':memberID', $memberID);
        $stmt->bindParam(':oldPass', $currentPassword);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Given a month, this function returns all accounts that were created
     * during that month for the year 2023 (current year)
     * @param $month
     * @return array
     */
    function getAccountsCreatedMonth($month)
    {
        //1. Define the query (does like even exist in sql? am I losing it?)
        $sql = 'select count(*) as count from members where join_date like :month/?/23';

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //3. Bind params
        $statement->bindParam(':month', $month);

        //4. Execute the query
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $statement->debugDumpParams();

        return $result['count'];
    }

    /**
     * @todo - sort the results by the month so that they are returned in order and can be iterated on by month
     * Given a year, this function returns all accounts that were created
     * during that year
     * @param $year
     * @return array|false
     *
     */
    function getAccountsCreatedYear($year)
    {
        //1. Define the query (not sure about like, using anyway to get pseudocode at least)
        $sql = "select count(*) as count from members where join_date like ?/?/:year";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //3. Bind the parameters
        $statement->bindParam(':year', $year);

        //4. Execute the query
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['count'];
    }

    /**
     * Grabs all member information for subscribed accounts
     * @return int
     */
    function getRevenue()
    {
        //1. Define the query
        $sql = "select * from members where membership_level > 0";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //4. Execute the query
        $statement->execute();

        //5. Process the results
        $revenue = 0;
        while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if ($row['membership_pay_period'] == 0) {
                if ($row['membership_level'] == 1) {
                    $revenue += 70;
                } else if ($row['membership_level'] == 2) {
                    $revenue += 90;
                } else {
                    $revenue += 120;
                }
            }else {
                if ($row['membership_level'] == 1) {
                    $revenue += 700 / 12;
                } else if ($row['membership_level'] == 2) {
                    $revenue += 900 / 12;
                } else {
                    $revenue += 1200 / 12;
                }
            }
        }

        $fmt = numfmt_create( 'en_US', NumberFormatter::CURRENCY );

        return numfmt_format_currency($fmt, $revenue, 'usd');
    }

    /**
     * Will be used when an admin needs to manually suspend
     * an acount.
     *
     * Downgrades an account from a member to a user,
     * effectively suspending their membership
     */
    function downgradeMember($id)
    {
        //1. Define the query
        $sql = "update members set membership_level = 0 where member_id = :id";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //3. Bind the parameters
        $statement->bindParam(':id', $id);

        //4. Execute the query
        $statement->execute();
    }

    /**
     * If the user has not visited today, runs a query on
     * the database inserting a new row into visits table
     * with a given member id, grabbing the current date/time
     * and time stamping the visit, returns an error message if
     * the user has already visited or a success message
     * if the visit was entered successfully
     * @param $id
     * @return string
     */
    function visit($id)
    {
        //1. Define the query
        $date = date('m/d/y');
        $sql = "select * from visits where member_id = :id AND visit_date like :date";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //3. Bind the parameters
        $statement->bindParam(':date', $date);

        //4. Execute the statement
        $statement->execute();

        //5. Process the results
        $hasVisited = false;
        if ($statement->rowCount() > 0) {
            $hasVisited = true;
        }

        if (!$hasVisited) {
            //1. Define the query
            $sql = "insert into visits (member_id, visit_date)
             values (:id, :date)";

            //2. Prepare the statement
            $statement=$this->_dbh->prepare($sql);

            $date = date('m/d/y h:i:s a', time());
            //3. Bind the parameters
            $statement->bindParam(':id', $id);
            $statement->bindParam(':date', $date);

            //4. Execute the statement
            $statement->execute();

            return "Visit successful";
        } else {
            return "You have already visited today, come back tomorrow!";
        }
    }

    /**
     * Runs a query on the database given a member id
     * to return that member's number of visits to the gym
     * @param $id
     * @return array
     */
    function getVisits($id)
    {
        //1. Define the query
        $sql = "select count(*) as count from visits where member_id = $id";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        // 4. Execute the query (no params to bind)
        $statement->execute();

        //5. Process the results
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['count'];
    }

    function getTotalVisits()
    {
        //1. Define the query
        $sql = "select count(*) as count from visits";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //4. Execute the query
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['count'];
    }

    function getTotalMembers()
    {
        $sql = "select count(*) as count from members";

        $statement = $this->_dbh->prepare($sql);

        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['count'];
    }

    /**
     * returns all Globo Gym members from database
     * @return array
     */
    function getMembers ()
    {
        //1. Define the query
        $sql = "SELECT * FROM members ORDER BY member_id ASC";

        //2. Prepare the statement
        $statement = $this->_dbh->prepare($sql);

        //3. Bind the parameters

        //4. Execute the query
        $statement->execute();

        //5. Process the results
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}