<?php

/**
 * Member class for the Globo Gym website, will be created when an existing user subscribes to a membership,
 * or a new user chooses a membership plan upon account creation
 */
class Member extends User
{
    private $_membershipLevel;
    private $_membershipPayPeriod;

    /**
     * Constructor for the Member class
     * @param $_membershipLevel
     * @param $_membershipPayPeriod
     * @param $_fName
     * @param $_lName
     * @param $_uName
     * @param $_password
     * @param $_email
     * @param $_phone
     * @param $_balance
     * @param $_joinDate
     */
    function __construct($_membershipLevel, $_membershipPayPeriod, $_fName = "", $_lName = "", $_uName = "", $_password = "", $_email = "", $_phone = "", $_balance = "", $_joinDate = "")
    {
        parent::__construct($_fName, $_lName, $_uName, $_password, $_email, $_phone, $_balance, $_joinDate);
        $this->_membershipLevel = $_membershipLevel;
        $this->_membershipPayPeriod = $_membershipPayPeriod;
    }

    /**
     * Gets and returns the member's membership level
     * @return mixed
     */
    public function getMembershipLevel()
    {
        return $this->_membershipLevel;
    }

    /**
     * Sets the member's membership level
     * @param mixed $membershipLevel
     */
    public function setMembershipLevel($membershipLevel): void
    {
        $this->_membershipLevel = $membershipLevel;
    }

    /**
     * Gets and returns the member's membership pay period
     * @return mixed
     */
    public function getMembershipPayPeriod()
    {
        return $this->_membershipPayPeriod;
    }

    /**
     * Sets the member's membership pay period
     * @param mixed $membershipPayPeriod
     */
    public function setMembershipPayPeriod($membershipPayPeriod): void
    {
        $this->_membershipPayPeriod = $membershipPayPeriod;
    }


}