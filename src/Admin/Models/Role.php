<?php
namespace Friparia\Admin\Models;
use Friparia\Admin\Model;
class Role extends Model{
    protected $title = "用户组";
    protected $unlistable = ['description', 'created_at', 'updated_at'];
    protected $uneditable = ['description', 'created_at', 'updated_at'];
    protected $actions = [
        'edit' => [
            'type' => 'modal',
            'color' => 'blue',
            'description' => '修改',
        ],
        'add' => [
            'type' => 'modal',
            'single' => true,
            'each' => false,
            'color' => 'green',
            'icon' => 'add',
            'description' => '添加',
        ],
    ];
    protected function construct(){
        $this->fields->string('name')->unique()->description("名称");
        $this->fields->string('description')->nullable();
        $this->fields->timestamps();
        $this->fields->relation('user')->belongsToMany('Friparia\\Admin\\Models\\User');
//        $this->fields->relation('permission')->hasManyToMany('Friparia\\Admin\\Models\\Permission');
    }

    public function permission(){
        return $this->belongsToMany('Friparia\\Admin\\Models\\Permission');
    }

    public function hasPermission($permission_name){
        foreach($this->permission as $permission){
            if($permission->name == $permission_name){
                return true;
            }
        }
        return false;
    }
}
