<?php

namespace IvanSotelo\OpenpayCashier\Tests\Feature;

use Illuminate\Database\Eloquent\Model as Eloquent;
use IvanSotelo\OpenpayCashier\Tests\Fixtures\User;
use IvanSotelo\OpenpayCashier\Tests\TestCase;
// use Stripe\ApiResource;
// use Stripe\Exception\InvalidRequestException;
// use Stripe\Stripe;

abstract class FeatureTestCase extends TestCase
{
    /**
     * @var string
     */
    protected static $openpayPrefix = 'cashier-test-';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Stripe::setApiKey(getenv('STRIPE_SECRET'));
    }

    protected function setUp(): void
    {
        // Delay consecutive tests to prevent Openpay rate limiting issues.
        sleep(2);

        parent::setUp();

        Eloquent::unguard();

        $this->loadLaravelMigrations();

        $this->artisan('migrate')->run();
    }

    protected static function deleteOpenpayResource(ApiResource $resource)
    {
        // try {
        //     $resource->delete();
        // } catch (InvalidRequestException $e) {
        //     //
        // }
    }

    protected function createCustomer($description = 'ivan', $options = []): User
    {
        return User::create(array_merge([
            'email' => "{$description}@cashier-test.com",
            'name' => 'Ivan Sotelo',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ], $options));
    }
}