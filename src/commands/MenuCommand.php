<?php
/**
 * Created by PhpStorm.
 * User: friparia
 * Date: 16/4/7
 * Time: 00:33
 */

namespace Friparia\Admin;
use Illuminate\Console\Command;
use Friparia\Admin\Models\Menu;


class MenuCommand extends Command
{

    protected $signature = "admin:menu {option=list} {name?}";

    protected $description = "Manage the admin menu";

    public function handle()
    {
        $option = $this->argument('option');
        if ($option == 'list') {
            if (Menu::count()) {
                $this->info("Name\tUrl");
            }
            foreach (Menu::all() as $menu) {
                $this->info("{$menu->name}\t{$menu->url}");
            }
        }elseif($option == 'create') {
            $name = $this->argument('name');
            if ($name == "") {
                $this->error("please enter name");
            }
            $pid = $this->ask("Please enter pid", 0);
            $url = $this->ask("Please enter url");
            $menu = new Menu;
            $menu->pid = $pid;
            $menu->name = $name;
            $menu->url = $url;
            $menu->save();
        }else{
            $this->error("Invalid option, please enter list, create or delete");
        }

    }
}
