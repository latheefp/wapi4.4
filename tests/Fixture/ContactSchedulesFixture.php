<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactSchedulesFixture
 */
class ContactSchedulesFixture extends TestFixture
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
                'schedule_id' => 1,
                'contact_waid' => 'Lorem ipsum d',
            ],
        ];
        parent::init();
    }
}
