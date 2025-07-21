<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;

//How to use.
#bin/cake reset_password <user> <password>   

class ResetPasswordCommand extends Command
{
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArgument('username', ['help' => 'Username of the user', 'required' => true])
            ->addArgument('password', ['help' => 'New password', 'required' => true]);
    }

    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $username = $args->getArgument('username');
        $newPassword = $args->getArgument('password');

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->find()->where(['username' => $username])->first();

        if (!$user) {
            $io->err("User '$username' not found.");
            return 1;
        }

        $hasher = new DefaultPasswordHasher();
       # $user->password = $hasher->hash($newPassword);
         $user->password = $newPassword;
        $user->password_link = null;
        $user->link_created = null;

        if ($usersTable->save($user)) {
            $io->out("Password for '$username' successfully reset as $newPassword");
            return 0;
        } else {
            $io->err("Failed to reset password.");
            return 1;
        }
    }
}