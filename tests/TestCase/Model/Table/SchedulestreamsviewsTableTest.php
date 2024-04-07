<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SchedulestreamsviewsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SchedulestreamsviewsTable Test Case
 */
class SchedulestreamsviewsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SchedulestreamsviewsTable
     */
    protected $Schedulestreamsviews;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Schedulestreamsviews',
        'app.Schedules',
        'app.Contacts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Schedulestreamsviews') ? [] : ['className' => SchedulestreamsviewsTable::class];
        $this->Schedulestreamsviews = $this->getTableLocator()->get('Schedulestreamsviews', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Schedulestreamsviews);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SchedulestreamsviewsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SchedulestreamsviewsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
