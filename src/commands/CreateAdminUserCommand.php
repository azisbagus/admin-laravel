<?php
/**
 * Created by PhpStorm.
 * User: friparia
 * Date: 16/3/31
 * Time: 00:31
 */

namespace Friparia\Admin;

use Friparia\Admin\Models\User;
use Illuminate\Console\Command;
use Hash;

class CreateAdminUserCommand extends Command
{
    protected $signature = "admin:create-superuser {name}";

    protected $description = "Create a super admin user account";

    public function handle(){
        $name = $this->argument('name');
        $password = $this->secret("What is the password?");
        $confirm = $this->secret("Please re-enter yout password");
        if($password != $confirm){
            $this->error("Please enter same password!");
        }
        User::create(['name' => $name, 'password' => Hash::make($password), 'is_admin' => 1]);
        $this->line("create success!");

    }

}
