<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ApiKeysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ApiKeysTable Test Case
 */
class ApiKeysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ApiKeysTable
     */
    protected $ApiKeys;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ApiKeys',
        'app.Users',
        'app.Accounts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ApiKeys') ? [] : ['className' => ApiKeysTable::class];
        $this->ApiKeys = $this->getTableLocator()->get('ApiKeys', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ApiKeys);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ApiKeysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ApiKeysTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
