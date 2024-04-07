<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StreamViewsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StreamViewsTable Test Case
 */
class StreamViewsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StreamViewsTable
     */
    protected $StreamViews;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.StreamViews',
        'app.ContactStreams',
        'app.Schedules',
        'app.Contacts',
        'app.Accounts',
        'app.Compaigns',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('StreamViews') ? [] : ['className' => StreamViewsTable::class];
        $this->StreamViews = $this->getTableLocator()->get('StreamViews', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->StreamViews);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\StreamViewsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\StreamViewsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
