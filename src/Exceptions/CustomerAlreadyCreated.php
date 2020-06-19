<?php

namespace IvanSotelo\OpenpayCashier\Exceptions;

use Exception;

class CustomerAlreadyCreated extends Exception
{
    /**
     * Create a new CustomerAlreadyCreated instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $owner
     * @return static
     */
    public static function exists($owner)
    {
        return new static(class_basename($owner)." is already a Openpay customer with ID {$owner->openpay_id}.");
    }
}
