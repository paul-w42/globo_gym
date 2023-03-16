<?php

/**
 * Member class for the Globo Gym website, will be created when an existing user subscribes to a membership,
 * or a new user chooses a membership plan upon account creation and the account does not have admin privileges
 */
class Member extends User
{
    private $_membershipLevel;
    private $_membershipPayPeriod;

    private $_membershipLevelName;

    private $_membershipPriceMonth;

    private $_membershipPriceYear;

    /**
     * Constructor for the Member class
     * @param $_membershipLevel
     * @param $_membershipPayPeriod
     * @param $_fName
     * @param $_lName
     * @param $_uName
     * @param $_password
     * @param $_email\\
     * @param $_phone
     * @param $_balance
     */
    function __construct($_membershipLevel, $_fName = "", $_lName = "", $_uName = "", $_password = "", $_email = "", $_phone = "", $_balance = "", $_membershipPayPeriod=0)
    {
        parent::__construct($_fName, $_lName, $_uName, $_password, $_email, $_phone, $_balance);
        $this->_membershipLevel = $_membershipLevel;
        $this->_membershipPayPeriod = $_membershipPayPeriod;
    }

    /**
     * @return mixed
     */
    public function getMembershipPriceMonth()
    {
        return $this->_membershipPriceMonth;
    }

    /**
     * @param mixed $membershipPriceMonth
     */
    public function setMembershipPriceMonth($membershipPriceMonth): void
    {
        $this->_membershipPriceMonth = $membershipPriceMonth;
    }

    /**
     * @return mixed
     */
    public function getMembershipPriceYear()
    {
        return $this->_membershipPriceYear;
    }

    /**
     * @param mixed $membershipPriceYear
     */
    public function setMembershipPriceYear($membershipPriceYear): void
    {
        $this->_membershipPriceYear = $membershipPriceYear;
    }



    /**
     * @return mixed
     */
    public function getMembershipLevelName()
    {
        return $this->_membershipLevelName;
    }

    /**
     * @param mixed $membershipLevelName
     */
    public function setMembershipLevelName($membershipLevelName): void
    {
        $this->_membershipLevelName = $membershipLevelName;
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

    public function upgradeMember($user, $membershipLevel) {
        $this->setMemberID($user->getMemberID());
        $this->setFName($user->getFName());
        $this->setLName($user->getLName());
        $this->setPassword($user->getPassword());
        $this->setEmail($user->getEmail());
        $this->setPhone($user->getPhone());
        $this->setBalance($user->getBalance());
        $this->setJoinDate($user->getJoinDate());

        if ($membershipLevel == 1) {
            $this->setMembershipPriceMonth(70);
            $this->setMembershipLevelName("Bronze");
        } else if ($membershipLevel == 2) {
            $this->setMembershipPriceMonth(90);
            $this->setMembershipLevelName("Silver");
        } else {
            $this->setMembershipPriceMonth(120);
            $this->setMembershipLevelName("Gold");
        }
        $this->setBalance($this->getMembershipPriceMonth());
        $this->setMembershipPriceYear($this->getMembershipPriceMonth() * 10);
        $this->setMembershipPayPeriod(0);
    }

}