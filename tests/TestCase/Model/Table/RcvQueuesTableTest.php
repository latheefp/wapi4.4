<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RcvQueuesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RcvQueuesTable Test Case
 */
class RcvQueuesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RcvQueuesTable
     */
    protected $RcvQueues;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.RcvQueues',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('RcvQueues') ? [] : ['className' => RcvQueuesTable::class];
        $this->RcvQueues = $this->getTableLocator()->get('RcvQueues', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->RcvQueues);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RcvQueuesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
