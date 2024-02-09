<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CommandsFixture
 */
class CommandsFixture extends TestFixture
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
                'account_id' => 1,
                'cmd' => 'Lorem ipsum do',
                'function' => 'Lorem ipsum dolor sit amet',
                'help_text' => 'Lorem ipsum do',
            ],
        ];
        parent::init();
    }
}
