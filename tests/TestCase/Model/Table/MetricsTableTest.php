<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MetricsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MetricsTable Test Case
 */
class MetricsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MetricsTable
     */
    protected $Metrics;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Metrics',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Metrics') ? [] : ['className' => MetricsTable::class];
        $this->Metrics = $this->getTableLocator()->get('Metrics', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Metrics);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\MetricsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
