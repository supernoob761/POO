<?php
class savingsAccount extends Accounts{

    public function abletowithdraw(float $money):bool
    {
        return $this->balance >= $money;
    }
}


?>