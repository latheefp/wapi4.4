<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactsContactNumbersFixture
 */
class ContactsContactNumbersFixture extends TestFixture
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
                'contact_number_id' => 1,
                'contact_id' => 1,
                'id' => 1,
            ],
        ];
        parent::init();
    }
}
