<?php

namespace IvanSotelo\OpenpayCashier\Tests\Feature;

class CustomerTest extends FeatureTestCase
{
    public function test_customers_in_openpay_can_be_updated()
    {
        $user = $this->createCustomer('customers_in_openpay_can_be_updated');
        // $user->createAsOpenpayCustomer();

        // $customer = $user->updateOpenpayCustomer(['description' => 'Mohamed Said']);

        $this->assertEquals('Mohamed Said', '$customer->description');
    }
}