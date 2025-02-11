<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactStreamsFixture
 */
class ContactStreamsFixture extends TestFixture
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
                'contact_number' => 'Lorem ipsum dolo',
                'profile_name' => 'Lorem ipsum dolor sit amet',
                'account_id' => 1,
                'camp_blocked' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'user_id' => 1,
                'created' => '2024-10-18 09:53:24',
            ],
        ];
        parent::init();
    }
}
