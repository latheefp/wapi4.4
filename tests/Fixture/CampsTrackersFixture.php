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
                'campaign_id' => 1,
                'contact_number_id' => 1,
                'created' => '2024-03-27 20:23:21',
                'lead' => 1,
                'leadtime' => '2024-03-27 20:23:21',
                'modified' => '2024-03-27 20:23:21',
                'hashvalue' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'duplicate_blocked' => 1,
            ],
        ];
        parent::init();
    }
}
