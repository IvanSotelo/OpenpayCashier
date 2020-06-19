<?php

namespace IvanSotelo\OpenpayCashier;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class OpenpayCashier
{
    /**
     * The OpenpayCashier library version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * The Openpay API version.
     *
     * @var string
     */
    const OPENPAY_VERSION = '2020-03-02';

    /**
     * The custom currency formatter.
     *
     * @var callable
     */
    protected static $formatCurrencyUsing;

    /**
     * Indicates if OpenpayCashier migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Indicates if OpenpayCashier routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Indicates if OpenpayCashier will mark past due subscriptions as inactive.
     *
     * @var bool
     */
    public static $deactivatePastDue = true;

    /**
     * Get the billable entity instance by Openpay ID.
     *
     * @param  string  $openpayId
     * @return \IvanSotelo\OpenpayCashier\Billable|null
     */
    public static function findBillable($openpayId)
    {
        if ($openpayId === null) {
            return;
        }

        $model = config('openpay-cashier.model');

        return (new $model)->where('openpay_id', $openpayId)->first();
    }

    /**
     * Get the default Openpay API options.
     *
     * @param  array  $options
     * @return array
     */
    public static function openpayOptions(array $options = [])
    {
        return array_merge([
            'id' => config('openpay-cashier.id'),
            'secret' => config('openpay-cashier.secret')
        ], $options);
    }

    /**
     * Set the custom currency formatter.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function formatCurrencyUsing(callable $callback)
    {
        static::$formatCurrencyUsing = $callback;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @param  string|null  $currency
     * @param  string|null  $locale
     * @return string
     */
    public static function formatAmount($amount, $currency = null, $locale = null)
    {
        if (static::$formatCurrencyUsing) {
            return call_user_func(static::$formatCurrencyUsing, $amount, $currency);
        }

        $money = new Money($amount, new Currency(strtoupper($currency ?? config('openpay-cashier.currency'))));

        $locale = $locale ?? config('openpay-cashier.currency_locale');

        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Configure OpenpayCashier to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Configure OpenpayCashier to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }

    /**
     * Configure OpenpayCashier to maintain past due subscriptions as active.
     *
     * @return static
     */
    public static function keepPastDueSubscriptionsActive()
    {
        static::$deactivatePastDue = false;

        return new static;
    }
}
