<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FlagshipsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FlagshipsTable Test Case
 */
class FlagshipsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FlagshipsTable
     */
    protected $Flagships;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Flagships',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Flagships') ? [] : ['className' => FlagshipsTable::class];
        $this->Flagships = $this->getTableLocator()->get('Flagships', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Flagships);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\FlagshipsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
