<?php
namespace Friparia\Admin;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class Relation extends Fluent
{
    public $name;
    public $related;
    public $foreignKey;
    public $otherKey = "id";
    public $localKey = "id";
    public $type;
    public $blueprint;
    public $table;
    const BELONGS_TO = 1;
    const HAS_ONE = 2;
    const HAS_MANY = 3;
    const MANY_TO_MANY = 4;
    const BELONGS_TO_MANY = 5;

    public function __construct($name, $blueprint){
        $this->name = $name;
        $this->blueprint = $blueprint;
    }

    public function belongsTo($parent, $foreignKey = "", $otherKey = ""){
        $this->related = $parent;
        if($foreignKey == ""){
            $foreignKey = $this->name . "_id";
        }
        if($otherKey == ""){
            $otherKey = "id";
        }
        $this->foreignKey = $foreignKey;
        $this->otherKey = $otherKey;
        $this->type = self::BELONGS_TO;
        $this->blueprint->integer($foreignKey);
        return $this;
    }

    public function hasOne($parent, $foreignKey = "", $localKey = ""){
        $this->related = $parent;
        if($foreignKey == ""){
            $foreignKey = $this->name . "_id";
        }
        if($localKey == ""){
            $localKey = "id";
        }
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->type = self::HAS_ONE;
        return $this;
    }

    //'App\Comment', 'foreign_key', 'local_key'
    public function hasMany($parent, $foreignKey = "", $localKey = ""){
        $this->related = $parent;
        if($foreignKey == ""){
            $foreignKey = $this->name . "_id";
        }
        if($localKey == ""){
            $localKey = "id";
        }
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->type = self::HAS_MANY;
        return $this;
    }

    //'App\Role', 'role_user', 'user_id', 'role_id'
    public function belongsToMany($related, $table = "", $foreignKey = "", $otherKey = ""){
        $this->related = $related;
        $this->table = $this->getJoinTable($table);
        if($foreignKey == ""){
            $foreignKey = Str::singular($this->blueprint->getTable()) . "_id";
        }
        if($otherKey == ""){
            $otherKey = Str::singular(class_basename($related)) . "_id";
        }
        $this->foreignKey = $foreignKey;
        $this->otherKey = $otherKey;
        $this->type = self::BELONGS_TO_MANY;
        return $this;
    }

    //'App\Role', 'role_user', 'user_id', 'role_id'
    public function hasManyToMany($related, $table = "", $foreignKey = "", $otherKey = ""){
        $this->related = $related;
        $this->table = $this->getJoinTable($table);
        if($foreignKey == ""){
            $foreignKey = Str::singular($this->blueprint->getTable()) . "_id";
        }
        if($otherKey == ""){
            $otherKey = $this->name . "_id";
        }
        $this->foreignKey = $foreignKey;
        $this->otherKey = $otherKey;
        $this->type = self::MANY_TO_MANY;
        return $this;
    }

    protected function getJoinTable($table = ""){
        if($table == ""){
            $arr = [Str::singular($this->blueprint->getTable()), $this->name];
            sort($arr);
            $table = implode("_", $arr);
        }
        return $table;
    }


    public function __call($method, $parameters)
    {
        return parent::__call($method, $parameters);
    }

}

