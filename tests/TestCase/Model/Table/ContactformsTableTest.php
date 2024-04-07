<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactformsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactformsTable Test Case
 */
class ContactformsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactformsTable
     */
    protected $Contactforms;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Contactforms',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Contactforms') ? [] : ['className' => ContactformsTable::class];
        $this->Contactforms = $this->getTableLocator()->get('Contactforms', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Contactforms);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ContactformsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
