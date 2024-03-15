<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CampsTrackersFixture
 */
class CampsTrackersFixture extends TestFixture
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
                'campain_id' => 1,
                'contact_stream_id' => 1,
                'created' => '2024-03-15 09:49:24',
                'lead' => 1,
                'modified' => '2024-03-15 09:49:24',
            ],
        ];
        parent::init();
    }
}
