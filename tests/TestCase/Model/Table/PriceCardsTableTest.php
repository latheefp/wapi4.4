<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PriceCardsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PriceCardsTable Test Case
 */
class PriceCardsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PriceCardsTable
     */
    protected $PriceCards;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.PriceCards',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('PriceCards') ? [] : ['className' => PriceCardsTable::class];
        $this->PriceCards = $this->getTableLocator()->get('PriceCards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->PriceCards);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\PriceCardsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
