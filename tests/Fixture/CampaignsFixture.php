<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CampaignsFixture
 */
class CampaignsFixture extends TestFixture
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
                'campaign_name' => 'Lorem ipsum dolor sit amet',
                'start_date' => '2024-03-17',
                'end_date' => '2024-03-17',
                'auto_inject' => 1,
                'inject_text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created' => '2024-03-17 20:58:06',
                'user_id' => 1,
                'template_id' => 1,
            ],
        ];
        parent::init();
    }
}
