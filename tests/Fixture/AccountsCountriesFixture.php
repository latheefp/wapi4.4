<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AccountsCountriesFixture
 */
class AccountsCountriesFixture extends TestFixture
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
                'account_id' => 1,
                'country_id' => 1,
                'created' => '2025-07-22 18:40:59',
            ],
        ];
        parent::init();
    }
}
