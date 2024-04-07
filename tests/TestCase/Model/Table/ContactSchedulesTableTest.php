<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactSchedulesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactSchedulesTable Test Case
 */
class ContactSchedulesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactSchedulesTable
     */
    protected $ContactSchedules;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ContactSchedules',
        'app.Schedules',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ContactSchedules') ? [] : ['className' => ContactSchedulesTable::class];
        $this->ContactSchedules = $this->getTableLocator()->get('ContactSchedules', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ContactSchedules);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ContactSchedulesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ContactSchedulesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
