<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SendQueuesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SendQueuesTable Test Case
 */
class SendQueuesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SendQueuesTable
     */
    protected $SendQueues;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SendQueues',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SendQueues') ? [] : ['className' => SendQueuesTable::class];
        $this->SendQueues = $this->getTableLocator()->get('SendQueues', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SendQueues);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SendQueuesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
