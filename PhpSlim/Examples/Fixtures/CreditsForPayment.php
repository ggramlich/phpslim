<?php
class Fixtures_CreditsForPayment
{
    public $payment;
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    public function credits()
    {
        return Lib_JukeBox::creditsFor($this->payment);
    }
}
