<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AccountsCountriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AccountsCountriesTable Test Case
 */
class AccountsCountriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AccountsCountriesTable
     */
    protected $AccountsCountries;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.AccountsCountries',
        'app.Accounts',
        'app.Countries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('AccountsCountries') ? [] : ['className' => AccountsCountriesTable::class];
        $this->AccountsCountries = $this->getTableLocator()->get('AccountsCountries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->AccountsCountries);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\AccountsCountriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\AccountsCountriesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
