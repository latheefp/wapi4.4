<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * InvoicesFixture
 */
class InvoicesFixture extends TestFixture
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
                'year' => 'Lo',
                'month' => 'Lo',
                'account_id' => 1,
                'invoice_number' => 'Lorem ipsum dolor ',
                'invoice_date' => '2024-05-09',
                'due_date' => '2024-05-09',
                'total_amount' => 1.5,
                'status' => 'Lorem ipsum dolor sit amet',
                'created' => 1715293073,
                'modified' => 1715293073,
            ],
        ];
        parent::init();
    }
}
