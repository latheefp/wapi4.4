<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ContactStreamsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ContactStreamsTable Test Case
 */
class ContactStreamsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ContactStreamsTable
     */
    protected $ContactStreams;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ContactStreams',
        'app.Accounts',
        'app.Users',
        'app.Chats',
        'app.RatingViews',
        'app.RecentChats',
        'app.StreamViews',
        'app.Streams',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ContactStreams') ? [] : ['className' => ContactStreamsTable::class];
        $this->ContactStreams = $this->getTableLocator()->get('ContactStreams', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ContactStreams);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ContactStreamsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ContactStreamsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
