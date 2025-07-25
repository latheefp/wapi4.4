<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddStatusAndLaunchedDateToFlagship extends AbstractMigration
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
        $table = $this->table('flagships');
        $table->addColumn('contains', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('contains_field', 'string', [
            'default' => null,
            'null' => true,
            'limit' => 255,
        ]);
        $table->update();
    }
}
