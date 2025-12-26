<?php
class savingsAccount extends Accounts{

     public function deposit(float $amount): void
    {
        $this->balance += $amount;
    }

    public function withdraw(float $amount): bool
    {
        if ($amount > $this->balance) {
            return false;
        }

        $this->balance -= $amount;
        return true;
    }
}


?>