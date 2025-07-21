<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RenamePhoneNumberIdColumn extends AbstractMigration
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
        $this->table('accounts') // replace with actual table name in snake_case
            ->renameColumn('phone_number_id', 'phone_numberId')
            ->update();
    }
}
