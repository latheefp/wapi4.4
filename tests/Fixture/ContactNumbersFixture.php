<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactNumbersFixture
 */
class ContactNumbersFixture extends TestFixture
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
                'mobile_number' => 'Lorem ipsu',
                'name' => 'Lorem ipsum dolor sit amet',
                'gender' => 'L',
                'expiry' => '2024-03-31',
                'whatsapp' => 1,
                'blocked' => 1,
            ],
        ];
        parent::init();
    }
}
