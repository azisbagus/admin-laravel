<?php
namespace Friparia\Admin\Models;
use Friparia\Admin\Model;
class Permission extends Model{
    protected function construct(){
        $this->fields->string('name')->unique();
        $this->fields->string('description')->nullable();
        $this->fields->timestamps();
        $this->fields->relation('role')->belongsToMany('Friparia\\Admin\\Models\\Role');
    }
}
