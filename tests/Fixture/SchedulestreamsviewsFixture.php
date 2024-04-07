<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SchedulestreamsviewsFixture
 */
class SchedulestreamsviewsFixture extends TestFixture
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
                'lang' => 'Lorem ',
                'sent_time' => '2024-03-27 00:14:34',
                'delivered_time' => '2024-03-27 00:14:34',
                'read_time' => '2024-03-27 00:14:34',
                'has_wa' => 1,
                'schedule_id' => 1,
                'contact_waid' => 'Lorem ipsum dolo',
            ],
        ];
        parent::init();
    }
}
