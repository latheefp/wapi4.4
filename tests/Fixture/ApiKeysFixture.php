<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ApiKeysFixture
 */
class ApiKeysFixture extends TestFixture
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
                'api_name' => 'Lorem ipsum dolor sit amet',
                'api_key' => 'Lorem ipsum dolor sit amet',
                'user_id' => 1,
                'enabled' => 1,
                'created' => '2025-07-25',
                'ip_list' => 'Lorem ipsum d',
                'account_id' => 1,
            ],
        ];
        parent::init();
    }
}
