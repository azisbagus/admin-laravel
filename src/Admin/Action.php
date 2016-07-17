<?php
namespace Friparia\Admin;

use Illuminate\Support\Fluent;

class Action
{
    protected $_name;
    protected $_type;
    protected $_color;
    protected $_icon;
    public function __construct($type, $name){
        $this->_type = $type;
        $this->_name = $name;
    }

    public function __call($method, $parameters){
        $name = "_".$method;
        $this->$name = count($parameters) > 0 ? $parameters[0] : true;
        return $this;
    }
}

