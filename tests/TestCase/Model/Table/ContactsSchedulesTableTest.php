<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactsSchedulesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactsSchedulesTable Test Case
 */
class ContactsSchedulesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactsSchedulesTable
     */
    protected $ContactsSchedules;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ContactsSchedules',
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
        $config = $this->getTableLocator()->exists('ContactsSchedules') ? [] : ['className' => ContactsSchedulesTable::class];
        $this->ContactsSchedules = $this->getTableLocator()->get('ContactsSchedules', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ContactsSchedules);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ContactsSchedulesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ContactsSchedulesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
