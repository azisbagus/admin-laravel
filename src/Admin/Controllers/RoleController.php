<?php

namespace Friparia\Admin\Controllers;

use Friparia\Admin\Controller as BaseController;
use Friparia\Admin\Models\Role;

class RoleController extends BaseController
{
    protected $model = 'Friparia\\Admin\\Models\\Role';
    protected $actions = ['create', 'update'];
    public function update($id){
        $input = \Request::input();
        $role = Role::find($id);
        if(!isset($input['name'])){
            \Request::session()->flash("error", "请输入名称");
            return response()->json();
        }
        $role->name = $input['name'];
        $role->save();
        if(isset($input['permission'])){
            $data = [];
            foreach($input['permission'] as $key => $value){
                if($value == 'on'){
                    $data[] = $key;
                }
            }
            $role->permission()->sync($data);
        }else{
            $role->permission()->sync([]);
        }
        $role->save();
        \Request::session()->flash("success", '操作成功');
        return response()->json();
    }

    public function create(){
        $input = \Request::input();
        $role = new Role;
        if(!isset($input['name'])){
            \Request::session()->flash("error", "请输入名称");
            return response()->json();
        }
        $role->name = $input['name'];
        $role->save();
        if(isset($input['permission'])){
            $data = [];
            foreach($input['permission'] as $key => $value){
                if($value == 'on'){
                    $data[] = $key;
                }
            }
            $role->permission()->attach($data);
        }
        $role->save();
        \Request::session()->flash("success", '操作成功');
        return response()->json();

    }
}

