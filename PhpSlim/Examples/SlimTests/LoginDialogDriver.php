<?php
class Examples_SlimTests_LoginDialogDriver
{
    private $_userName;
    private $_password;
    private $_message;
    private $_loginAttempts = 0;

    public function __construct($userName, $password)
    {
        $this->_userName = $userName;
        $this->_password = $password;
    }

    public function loginWithUsernameAndPassword($userName, $password)
    {
        $this->_loginAttempts++;
        $result = ($this->_userName == $userName)
            && ($this->_password == $password);
        if ($result) {
            $this->_message = sprintf("%s logged in.", $this->_userName);
        } else {
            $this->_message = sprintf("%s not logged in.", $this->_userName);
        }
        return PhpSlim_TypeConverter::toString($result);
    }

    public function loginMessage()
    {
        return $this->_message;
    }

    public function numberOfLoginAttempts()
    {
        return $this->_loginAttempts;
    }
}
