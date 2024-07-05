<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChatsSessionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChatsSessionsTable Test Case
 */
class ChatsSessionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChatsSessionsTable
     */
    protected $ChatsSessions;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ChatsSessions',
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
        $config = $this->getTableLocator()->exists('ChatsSessions') ? [] : ['className' => ChatsSessionsTable::class];
        $this->ChatsSessions = $this->getTableLocator()->get('ChatsSessions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ChatsSessions);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ChatsSessionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ChatsSessionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
