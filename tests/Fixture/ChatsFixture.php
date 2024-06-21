<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChatsFixture
 */
class ChatsFixture extends TestFixture
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
                'clientid' => 1,
                'account_id' => 1,
                'user_id' => 1,
                'token' => 'Lorem ipsum dolor sit amet',
                'active' => 1,
                'created' => '2024-06-18 10:51:52',
                'modified' => '2024-06-18 10:51:52',
            ],
        ];
        parent::init();
    }
}
