<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateMessages extends AbstractMigration
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
        $table = $this->table('messages');
        $table->addColumn('user_id', 'integer', ['null' => false])
              ->addColumn('content', 'text', ['null' => false])
              ->addColumn('created', 'datetime', ['default' => null])
              ->addIndex(['user_id'])
              ->create();
    }
}
