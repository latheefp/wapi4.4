<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactsFixture
 */
class ContactsFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'contact_count' => 1,
                'whatsapp_count' => 1,
                'blocked_count' => 1,
                'created' => '2024-10-29 18:37:04',
                'account_id' => 1,
                'user_id' => 1,
            ],
        ];
        parent::init();
    }
}
