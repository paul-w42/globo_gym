<?php

/**
 * Admin class for the Globo Gym website, will be created when the user logs into an admin account
 */
class Admin extends User
{
    private $_adminID;
    private $_adminPrivileges;

    /**
     * Constructor for the Admin class
     * @param $_adminID
     * @param string $_fName
     * @param string $_lName
     * @param string $_uName
     * @param string $_password
     * @param string $_email
     * @param string $_phone
     * @param string $_balance
     * @param string $_joinDate
     */
    public function __construct($_adminID, $_fName="", $_lName="", $_uName="", $_password="", $_email="", $_phone="", $_balance="", $_joinDate="")
    {
        parent::__construct($_fName, $_lName, $_uName, $_password, $_email, $_phone, $_balance, $_joinDate);
        $this->_adminID = $_adminID;
        $this->_adminPrivileges = true;
    }

    /**
     * Gets and returns the admin's ID
     * @return mixed
     */
    public function getAdminID()
    {
        return $this->_adminID;
    }

    /**
     * Sets the admin's ID
     * @param mixed $adminID
     */
    public function setAdminID($adminID): void
    {
        $this->_adminID = $adminID;
    }

    /**
     * Gets and returns whether the account has admin privileges
     * @return bool
     */
    public function isAdminPrivileges(): bool
    {
        return $this->_adminPrivileges;
    }

    /**
     * Sets whether the account has admin privileges
     * @param bool $adminPrivileges
     */
    public function setAdminPrivileges(bool $adminPrivileges): void
    {
        $this->_adminPrivileges = $adminPrivileges;
    }


}