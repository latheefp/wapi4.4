<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BlockedNumbersFixture
 */
class BlockedNumbersFixture extends TestFixture
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
                'mobile_number' => 'Lorem ipsum do',
                'account_id' => 1,
                'created' => '2024-10-17 17:06:03',
                'user_id' => 1,
            ],
        ];
        parent::init();
    }
}
