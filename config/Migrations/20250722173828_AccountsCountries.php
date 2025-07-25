<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AccountsCountries extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
   public function change()
    {
        $table = $this->table('accounts_countries', [
            'engine' => 'InnoDB', // Explicitly set engine
            'collation' => 'utf8mb4_unicode_ci' // Match common collation
        ]);
        $table
      ->addColumn('account_id', 'integer', [
                'null' => false,
                'signed' => true // or just remove 'signed' to default to signed
            ])
            ->addColumn('country_id', 'integer', [
                'null' => false,
                'signed' => true
            ])
                        ->addColumn('created', 'datetime', [
                'null' => true
            ])
            ->addForeignKey('account_id', 'accounts', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('country_id', 'countries', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['account_id', 'country_id'], ['unique' => true])
            ->create();
    }
}


