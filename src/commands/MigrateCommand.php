<?php

namespace Friparia\Admin;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use DB;

class MigrateCommand extends Command
{
    protected $signature = "admin:migrate";

    protected $description = "Create or update database according to the model definition";

    public function handle(){
        $path = app_path();
        $allFiles = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)), '/\.php$/');
        $fields = [];
        if($this->confirm("Ready to migrate model")){
            foreach($allFiles as $file){
                $tokens = token_get_all(file_get_contents($file->getRealPath()));
                for($i = 0; $i < count($tokens); $i++){
                    if($tokens[$i][0] === T_CLASS){
                        for ($j = $i + 1; $j < count($tokens); $j++) {
                            if ($tokens[$j] === '{') {
                                $class = $tokens[$i+2][1];
                            }
                        }
                    }
                    if($tokens[$i][0] === T_NAMESPACE){
                        $namespace = "";
                        for ($j = $i + 1; $j < count($tokens); $j++) {
                            if ($tokens[$j + 1] === ';') {
                                break;
                            }
                            $namespace .= ($tokens[$j + 1][1]);
                        }
                    }
                }
                if(in_array($namespace, ["App", "App\\Models"])){
                    $className = $namespace."\\".$class;
                    $instance = new $className;
                    if($instance instanceof Model){
                        $fields[$instance->getTable()] = $instance->getFields();
                        $migrate = new Migrate($className);
                        $migrate->migrate();
                        $this->line("Migrate $className success!");
                    }
                }
            }
        }
    }
}

