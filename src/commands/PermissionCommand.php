<?php

namespace Friparia\Admin;

use Friparia\Admin\Models\Permission;
use Illuminate\Console\Command;

class PermissionCommand extends Command
{
    protected $signature = "admin:permission {option=list} {name?}";

    protected $description = "Create a permission";

    public function handle(){
        $option = $this->argument('option');
        if ($option == 'list') {
            if (Permission::count()) {
                $this->info("Name\tDescription");
            }
            foreach (Permission::all() as $permission) {
                $this->info("{$permission->name}\t{$permission->description}");
            }
        }elseif($option == 'create') {
            $name = $this->argument('name');
            if ($name == "") {
                $this->error("please enter name");
            }
            $description = $this->ask("Please enter description");
            $permission = new Permission;
            $permission->name = $name;
            $permission->description = $description;
            $permission->save();
        }else{
            $this->error("Invalid option, please enter list, create or delete");
        }
    }

}
