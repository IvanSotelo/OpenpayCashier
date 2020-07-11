<?php

namespace IvanSotelo\OpenpayCashier\Concerns;

use IvanSotelo\OpenpayCashier\OpenpayCashier;
use IvanSotelo\OpenpayCashier\Exceptions\CustomerAlreadyCreated;
use IvanSotelo\OpenpayCashier\Exceptions\InvalidCustomer;
use Openpay\Customer as OpenpayCustomer;

trait ManagesCustomer
{
    /**
     * Retrieve the Openpay customer ID.
     *
     * @return string|null
     */
    public function openpayId()
    {
        return $this->openpay_id;
    }

    /**
     * Determine if the entity has a Openpay customer ID.
     *
     * @return bool
     */
    public function hasOpenpayId()
    {
        return ! is_null($this->openpay_id);
    }

    /**
     * Determine if the entity has a Openpay customer ID and throw an exception if not.
     *
     * @return void
     *
     * @throws \IvanSotelo\OpenpayCashier\Exceptions\InvalidCustomer
     */
    protected function assertCustomerExists()
    {
        if (! $this->hasOpenpayId()) {
            throw InvalidCustomer::notYetCreated($this);
        }
    }

    /**
     * Create a Openpay customer for the given model.
     *
     * @param  array  $options
     * @return \Openpay\Customer
     *
     * @throws \IvanSotelo\OpenpayCashier\Exceptions\CustomerAlreadyCreated
     */
    public function createAsOpenpayCustomer(array $options = [])
    {
        if ($this->hasOpenpayId()) {
            throw CustomerAlreadyCreated::exists($this);
        }

        if (! array_key_exists('email', $options) && $email = $this->openpayEmail()) {
            $options['email'] = $email;
        }

        // Here we will create the customer instance on Openpay and store the ID of the
        // user from Openpay. This ID will correspond with the Openpay user instances
        // and allow us to retrieve users from Openpay later when we need to work.
        $customer = OpenpayCustomer::create(
            $options, $this->openpayOptions()
        );

        $this->openpay_id = $customer->id;

        $this->save();

        return $customer;
    }

    /**
     * Update the underlying Openpay customer information for the model.
     *
     * @param  array  $options
     * @return \Openpay\Customer
     */
    public function updateOpenpayCustomer(array $options = [])
    {
        return OpenpayCustomer::update(
            $this->openpay_id, $options, $this->openpayOptions()
        );
    }

    /**
     * Get the Openpay customer instance for the current user or create one.
     *
     * @param  array  $options
     * @return \Openpay\Customer
     */
    public function createOrGetOpenpayCustomer(array $options = [])
    {
        if ($this->hasOpenpayId()) {
            return $this->asOpenpayCustomer();
        }

        return $this->createAsOpenpayCustomer($options);
    }

    /**
     * Get the Openpay customer for the model.
     *
     * @return \Openpay\Customer
     */
    public function asOpenpayCustomer()
    {
        $this->assertCustomerExists();

        return OpenpayCustomer::retrieve($this->openpay_id, $this->openpayOptions());
    }

    /**
     * Get the email address used to create the customer in Openpay.
     *
     * @return string|null
     */
    public function openpayEmail()
    {
        return $this->email;
    }

    /**
     * Apply a coupon to the billable entity.
     *
     * @param  string  $coupon
     * @return void
     */
    public function applyCoupon($coupon)
    {
        $this->assertCustomerExists();

        $customer = $this->asOpenpayCustomer();

        $customer->coupon = $coupon;

        $customer->save();
    }

    /**
     * Get the Openpay supported currency used by the entity.
     *
     * @return string
     */
    public function preferredCurrency()
    {
        return config('cashier.currency');
    }

    /**
     * Determine if the customer is not exempted from taxes.
     *
     * @return bool
     */
    public function isNotTaxExempt()
    {
        return $this->asOpenpayCustomer()->tax_exempt === OpenpayCustomer::TAX_EXEMPT_NONE;
    }

    /**
     * Determine if the customer is exempted from taxes.
     *
     * @return bool
     */
    public function isTaxExempt()
    {
        return $this->asOpenpayCustomer()->tax_exempt === OpenpayCustomer::TAX_EXEMPT_EXEMPT;
    }

    /**
     * Determine if reverse charge applies to the customer.
     *
     * @return bool
     */
    public function reverseChargeApplies()
    {
        return $this->asOpenpayCustomer()->tax_exempt === OpenpayCustomer::TAX_EXEMPT_REVERSE;
    }

    /**
     * Get the default Openpay API options for the current Billable model.
     *
     * @param  array  $options
     * @return array
     */
    public function openpayOptions(array $options = [])
    {
        return OpenpayCashier::openpayOptions($options);
    }
}
