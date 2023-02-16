<?php


    require '/home/paulwood/db-globogym.php';
    /*
     * For mysqli bind_param, type specification vars
     * i = int
     * d = float
     * s = string
     * b = blob
    */
    function addCustomer($fname, $lname, $password, $email, $phone, $username) {

        global $cnxn;

        $sql = "insert into customers (first_name, last_name, user_name, login_password, join_date, email, phone) 
            values (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($cnxn, $sql);

        $joinDate = date('Y-m-d'); // https://www.php.net/manual/en/function.date.php#85692

        mysqli_stmt_bind_param($stmt,"sssssss", $fname, $lname, $username, $password, $joinDate, $email, $phone);
        mysqli_stmt_execute($stmt);

        $returnID = 0;

        if(mysqli_stmt_affected_rows($stmt) > 0) {
            $returnID = mysqli_insert_id($cnxn);
        }

        //mysqli_stmt_close($stmt);
        //mysqli_close($cnxn);

        //echo "AddCustomer(), Returning New CustomerID: " . $returnID . "<br>\n";

        return $returnID;
    }


    /*
     * Example function from prior project using prepared statement
     */
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
