<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactsContactNumbersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactsContactNumbersTable Test Case
 */
class ContactsContactNumbersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactsContactNumbersTable
     */
    protected $ContactsContactNumbers;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ContactsContactNumbers',
        'app.ContactNumbers',
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
        $config = $this->getTableLocator()->exists('ContactsContactNumbers') ? [] : ['className' => ContactsContactNumbersTable::class];
        $this->ContactsContactNumbers = $this->getTableLocator()->get('ContactsContactNumbers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ContactsContactNumbers);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ContactsContactNumbersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ContactsContactNumbersTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
