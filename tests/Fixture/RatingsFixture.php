<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RatingsFixture
 */
class RatingsFixture extends TestFixture
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
                'stream_id' => 1,
                'old_balance' => 1,
                'new_balance' => 1,
                'cost' => 1,
                'country' => 'Lorem ipsum dolor sit amet',
                'charging_status' => 1,
                'tax' => 1,
                'p_perc' => 1,
                'fb_cost' => 1,
                'conversation' => 'Lorem ipsum dolor sit amet',
                'rate_with_tax' => 1,
                'invoice_id' => 1,
                'created' => '2024-04-07 10:57:30',
            ],
        ];
        parent::init();
    }
}
