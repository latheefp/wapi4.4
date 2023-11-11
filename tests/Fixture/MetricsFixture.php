<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MetricsFixture
 */
class MetricsFixture extends TestFixture
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
                'module_name' => 'Lorem ipsum dolor sit amet',
                'account' => 1,
                'metric_value' => 1.5,
                'recorded_at' => 1699374554,
            ],
        ];
        parent::init();
    }
}
