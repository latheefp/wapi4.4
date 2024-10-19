<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BlockedNumbersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BlockedNumbersTable Test Case
 */
class BlockedNumbersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BlockedNumbersTable
     */
    protected $BlockedNumbers;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.BlockedNumbers',
        'app.Accounts',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('BlockedNumbers') ? [] : ['className' => BlockedNumbersTable::class];
        $this->BlockedNumbers = $this->getTableLocator()->get('BlockedNumbers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->BlockedNumbers);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\BlockedNumbersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\BlockedNumbersTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
