<?php
namespace Friparia\Admin;

use Illuminate\Support\Fluent;

class Field 
{
    protected $_type;
    protected $_name;
    protected $_description;

    public function __construct($type, $name){
        $this->_type = $type;
        $this->_name = $name;
    }

    public function __call($method, $parameters){
        $name = "_".$method;
        $this->$name = count($parameters) > 0 ? $parameters[0] : true;
        return $this;
    }

    public function getMigrationDescription(){
        $parameters = [];
        $types = [
            'bigIncrements',
            'bigInteger',
            'binary',
            'boolean',
            'char',
            'date',
            'dateTime',
            'dateTimeTz',
            'decimal',
            'double',
            'enum',
            'float',
            'integer',
            'ipAddress',
            'json',
            'jsonb',
            'longText',
            'macAddress',
            'mediumInteger',
            'mediumText',
            'morphs',
            'smallInteger',
            'string',
            'text',
            'time',
            'timeTz',
            'tinyInteger',
            'timestamp',
            'timestampTz',
            'uuid',
        ];
        if(in_array($this->_type, $types)){
            $method = $this->_type;
            return [$method, $this->_name, $parameters];
        }
        return false;
    }

}
