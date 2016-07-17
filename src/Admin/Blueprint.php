<?php
namespace Friparia\Admin;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;

class Blueprint extends LaravelBlueprint
{

    protected $relations = [];

    public function relation($name){
        $relation = new Relation($name, $this);
        $this->relations[$name] = $relation;
        return $relation;
    }

    public function getRelations(){
        return $this->relations;
    }

}


