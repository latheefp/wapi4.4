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
                'invoice_date' => '2024-01-21',
                'due_date' => '2024-01-21',
                'total_amount' => 1.5,
                'status' => 'Lorem ipsum dolor sit amet',
                'created' => 1705860723,
                'modified' => 1705860723,
            ],
        ];
        parent::init();
    }
}
