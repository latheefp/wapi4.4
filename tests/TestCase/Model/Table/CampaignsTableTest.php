<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CampaignsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CampaignsTable Test Case
 */
class CampaignsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CampaignsTable
     */
    protected $Campaigns;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Campaigns',
        'app.Users',
        'app.Templates',
        'app.CampaignForms',
        'app.ScheduleViews',
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
        $config = $this->getTableLocator()->exists('Campaigns') ? [] : ['className' => CampaignsTable::class];
        $this->Campaigns = $this->getTableLocator()->get('Campaigns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Campaigns);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CampaignsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\CampaignsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
