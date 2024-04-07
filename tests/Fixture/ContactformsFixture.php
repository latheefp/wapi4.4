<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ContactformsFixture
 */
class ContactformsFixture extends TestFixture
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
                'first_name' => 'Lorem ipsum dolo',
                'last_name' => 'Lorem ipsum dolo',
                'email' => 'Lorem ipsum dolor sit amet',
                'company' => 'Lorem ipsum dolor sit amet',
                'contact_number' => 'Lorem ipsum dolor ',
                'msg_per_month' => 1,
                'msg_per_day' => 1,
                'target_country' => 'Lorem ipsum dolor sit amet',
                'details' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created' => '2024-03-31 22:17:27',
            ],
        ];
        parent::init();
    }
}
