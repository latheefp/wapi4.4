<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddWebhookUrlAndVerifiedToAccounts extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('accounts');
        $table->addColumn('webhook_token', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('webhookverified', 'boolean', [
            'default' => null,
            'null' => false,
        ]);
        $table->update();
    }
}
