<?php
/**
 * Created by PhpStorm.
 * User: friparia
 * Date: 16/3/31
 * Time: 00:31
 */

namespace Friparia\Admin;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    protected $signature = "admin:setup";

    protected $description = "set up admin with role based access control";

    public function handle(){
        $migrate = new Migrate("Friparia\\Admin\\Models\\User");
        $migrate->migrate();
        $migrate = new Migrate("Friparia\\Admin\\Models\\Role");
        $migrate->migrate();
        $migrate = new Migrate("Friparia\\Admin\\Models\\Permission");
        $migrate->migrate();
        $migrate = new Migrate("Friparia\\Admin\\Models\\Menu");
        $migrate->migrate();
        $this->line("create success!");

    }

}
