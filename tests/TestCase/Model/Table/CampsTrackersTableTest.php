<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CampsTrackersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CampsTrackersTable Test Case
 */
class CampsTrackersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CampsTrackersTable
     */
    protected $CampsTrackers;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.CampsTrackers',
        'app.Campaigns',
        'app.ContactNumbers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CampsTrackers') ? [] : ['className' => CampsTrackersTable::class];
        $this->CampsTrackers = $this->getTableLocator()->get('CampsTrackers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CampsTrackers);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CampsTrackersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CampsTrackersTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
