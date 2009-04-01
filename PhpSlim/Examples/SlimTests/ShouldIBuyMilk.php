<?php
class Examples_SlimTests_ShouldIBuyMilk
{
    private $_dollars;
    private $_pints;
    private $_creditCard;

    public function setCashInWallet($dollars)
    {
        $this->_dollars = $dollars;
    }

    public function setPintsOfMilkRemaining($pints)
    {
        $this->_pints = $pints;
    }

    public function setCreditCard($valid)
    {
        $this->_creditCard = ("yes" == $valid);
    }

    public function goToStore()
    {
        $canPay = $this->_dollars > 2 || $this->_creditCard;
        return ($this->_pints == 0 && $canPay) ? "yes" : "no";
    }

    // The following functions are optional.
    // If they aren't declared they'll be ignored.
    public function execute()
    {
    }

    public function reset()
    {
    }

    public function table($table)
    {
    }
}
