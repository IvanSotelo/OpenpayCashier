<?php

namespace IvanSotelo\OpenpayCashier\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Model;
use Illuminate\Notifications\Notifiable;
use IvanSotelo\OpenpayCashier\Billable;

class User extends Model
{
    use Billable, Notifiable;

    public $taxRates = [];

    public $planTaxRates = [];

    /**
     * Get the tax rates to apply to the subscription.
     *
     * @return array
     */
    public function taxRates()
    {
        return $this->taxRates;
    }

    /**
     * Get the tax rates to apply to individual subscription items.
     *
     * @return array
     */
    public function planTaxRates()
    {
        return $this->planTaxRates;
    }
}
