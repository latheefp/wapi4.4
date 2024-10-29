<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PriceCardsFixture
 */
class PriceCardsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'country' => 'Lorem ipsum dolor sit amet',
                'country_code' => 'Lorem ips',
                'marketing' => 1,
                'utility' => 1,
                'authentication' => 1,
                'service' => 1,
                'business_Initiated_rate' => 1,
                'authentication_international' => 1,
                'user_Initiated_rate' => 1,
                'modified' => '2024-10-20 19:06:24',
                'market' => 'Lorem ipsum dolor sit amet',
                'updated' => 1,
            ],
        ];
        parent::init();
    }
}
