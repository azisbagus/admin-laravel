<?php

namespace Friparia\Admin\Models;

use Friparia\Admin\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;
    protected $title = "员工";
    protected $unlistable = ['password', 'is_admin', 'created_at', 'updated_at', 'remember_token', 'email', 'name'];
    protected $uneditable = ['password', 'is_admin', 'created_at', 'updated_at', 'remember_token', 'email', 'group', 'name'];
    protected $searchable = ['cname', 'cellphone'];
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
    protected $extended = ['group'];
    protected function construct(){
        $this->fields->string('name')->description("账号");
        $this->fields->string('cname')->description("姓名");
        $this->fields->string('cellphone')->description("电话");
        $this->fields->string('password');
        $this->fields->boolean('is_admin')->default(false);
        $this->fields->timestamps();
        $this->fields->rememberToken();
    }

    public function role(){
        return $this->belongsToMany('Friparia\\Admin\\Models\\Role');
    }

    public function getColumnDescription($name){
        if($name == 'group'){
            return "用户组";
        }
        return parent::getColumnDescription($name);
    }

    public function getValue($name){
        if($name == 'group'){
            $data = [];
            foreach($this->role as $role){
                $data[] = $role->name;
            }
            return implode(',', $data);
        }

        return parent::getValue($name);
    }

    public function hasPermission($permission_name){
        if($this->is_admin){
            return true;
        }
        if(!count(Permission::where('name', $permission_name)->get())){
            return true;
        };
        foreach($this->role as $role){
            if($role->hasPermission($permission_name)){
                return true;
            }
        }
        return false;
    }

    public function hasRole($role_id){
        foreach($this->role as $role){
            if($role->id == $role_id){
                return true;
            }
        }
        return false;
    }

    public function canVisit($url){
        $segments = explode('?', $url);
        $uri = $segments[0];
        return $this->hasPermission(implode('.', explode('/', $uri)));
    }
}
