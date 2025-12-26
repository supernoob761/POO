<?php
class checkingsAccount extends Accounts{

    public function deposit(float $amount): void
    {
        $this->balance += ($amount - 10);
    }

    public function withdraw(float $amount): bool
    {
        if (($this->balance - $amount) < -5000) {
            return false;
        }

        $this->balance -= $amount;
        return true;
    }
}


?>