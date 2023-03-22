<?php

/**
 * User class for the Globo Gym website, this class will be used when
 * the user decides to make an account without subscribing to a membership plan
 * and that is not an admin account
 */
class User
{
    private $_memberID;
    private $_fName;
    private $_lName;
    private $_uName;
    private $_password;
    private $_email;
    private $_phone;
    private $_balance;
    private $_joinDate;

    private $_visits;


    /**
     * Constructor for the User class
     * @param $_fName
     * @param $_lName
     * @param $_uName
     * @param $_password
     * @param $_email
     * @param $_phone
     * @param $_balance
     */
    public function __construct($_fName="", $_lName="", $_uName="", $_password="", $_email="", $_phone="", $_balance="")
    {
        $this->_fName = $_fName;
        $this->_lName = $_lName;
        $this->_uName = $_uName;
        $this->_password = $_password;
        $this->_email = $_email;
        $this->_phone = $_phone;
        $this->_balance = $_balance;
        $this->_joinDate = date('Y-m-d');
    }

    /**
     * Gets and returns the user's member ID
     * @return mixed
     */
    public function getMemberID()
    {
        return $this->_memberID;
    }

    /**
     * Sets the user's member ID
     * @param mixed $memberID
     */
    public function setMemberID($memberID): void
    {
        $this->_memberID = $memberID;
    }

    /**
     * Gets and returns the user's first name
     * @return mixed
     */
    public function getFName()
    {
        return $this->_fName;
    }

    /**
     * Sets the user's first name
     * @param mixed $fName
     */
    public function setFName($fName): void
    {
        $this->_fName = $fName;
    }

    /**
     * Gets and returns the user's last name
     * @return mixed
     */
    public function getLName()
    {
        return $this->_lName;
    }

    /**
     * Sets the user's last name
     * @param mixed $lName
     */
    public function setLName($lName): void
    {
        $this->_lName = $lName;
    }

    /**
     * Gets and returns the user's password
     * @return mixed
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Sets the user's password
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->_password = $password;
    }

    /**
     * Gets and returns the user's email
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Sets the user's email
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->_email = $email;
    }

    /**
     * Gets and returns the user's phone
     * @return mixed
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * Sets the usser's phone
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->_phone = $phone;
    }

    /**
     * Gets and returns the user's balance
     * @return mixed
     */
    public function getBalance()
    {
        return $this->_balance;
    }

    /**
     * Sets the user's balance
     * @param mixed $balance
     */
    public function setBalance($balance): void
    {
        $this->_balance = $balance;
    }

    /**
     * Gets and returns the user's join date
     * @return mixed
     */
    public function getJoinDate()
    {
        return $this->_joinDate;
    }

    /**
     * Sets the user's join date
     * @param mixed $joinDate
     */
    public function setJoinDate($joinDate): void
    {
        $this->_joinDate = $joinDate;
    }

    /**
     * Gets and returns the user's username
     * @return mixed
     */
    public function getUName()
    {
        return $this->_uName;
    }

    /**
     * Sets the user's username
     * @param mixed $uName
     */
    public function setUName($uName): void
    {
        $this->_uName = $uName;
    }

    /**
     * @return mixed
     */
    public function getVisits()
    {
        return $this->_visits;
    }

    /**
     * @param mixed $visits
     */
    public function setVisits($visits): void
    {
        $this->_visits = $visits;
    }


}