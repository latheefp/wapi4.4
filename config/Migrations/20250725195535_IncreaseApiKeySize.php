<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class IncreaseApiKeySize extends AbstractMigration
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
        $this->table('api_keys')
            ->changeColumn('api_key', 'string', [
                'limit' => 512, // or 1024, depending on your needs
                'null' => true,
            ])
            ->update();
    }
}
