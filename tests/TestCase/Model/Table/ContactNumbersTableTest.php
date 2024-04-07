<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactNumbersTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactNumbersTable Test Case
 */
class ContactNumbersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactNumbersTable
     */
    protected $ContactNumbers;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ContactNumbers',
        'app.CampsTrackers',
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
        $config = $this->getTableLocator()->exists('ContactNumbers') ? [] : ['className' => ContactNumbersTable::class];
        $this->ContactNumbers = $this->getTableLocator()->get('ContactNumbers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ContactNumbers);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ContactNumbersTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
