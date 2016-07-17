<?php
namespace Friparia\Admin;

use Illuminate\Database\Migrations\MigrationCreator as LaravelMigrationCreator; 
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends LaravelMigrationCreator{

    protected $_model;
    public function __construct($model){
        $this->_model = $model;
        parent::__construct(new Filesystem());
    }

    protected function getStub($table, $create){
        return $this->files->get(__DIR__.'/../stubs/migration.stub');
    }

    protected function populateStub($name, $stub, $table){
        $stub = parent::populateStub($name, $stub, $table);
        $columns = [];
        foreach($this->_model->getFields() as $field){
            if($description = $field->getMigrationDescription()){
                list($method, $name, $parameters) = $description;
                $columns[] = $this->populateColumnStub($method, $name, $parameters);
            }
        }
        $stub = str_replace('DummyColumns', implode("", $columns), $stub);
        return $stub;
    }

    protected function populateColumnStub($method, $name, $parameters){
        $stub = $this->files->get(__DIR__.'/../stubs/column.stub');
        $stub = str_replace('DummyMethod', $method, $stub);
        $stub = str_replace('DummyName', $name, $stub);
        if(empty($parameters)){
            $stub = str_replace('DummyParameters', "", $stub);
        }else{
            $parameters = implode(", ", $parameters);
            $stub = str_replace('DummyParameters', ", ".$parameters, $stub);
        }
        return $stub;
    }
}
