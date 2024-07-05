<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChatsSessionsFixture
 */
class ChatsSessionsFixture extends TestFixture
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
                'created' => '2024-06-26 17:48:24',
                'modified' => '2024-06-26 17:48:24',
            ],
        ];
        parent::init();
    }
}
