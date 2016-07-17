<?php
namespace Friparia\Admin;

use Route as LaravelRoute;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;
use App\Models\Log;

class Controller extends LaravelController
{


    protected $model;
    protected $actions = [];

    public function index($action, $id = null)
    {
        dd($action, $id);
    }

    private function logActions(){
        return [
            'create' => '创建',
            'update' => '编辑',
            'delete' => '删除',
        ];
    }
    public function admin(Request $request, $action, $id = null)
    {
        $instance = $this->initInstance($id);
        if(in_array($action, array_keys($this->logActions())) && $instance->getTitle() != ""){
            Log::add($instance->getTitle().$this->logActions()[$action]);
        }
        if(in_array($action, $this->actions)){
            if(is_null($id)){
                return $this->$action();
            }else{
                return $this->$action($id);
            }
        }
        if(is_null($instance)){
            $request->session()->flash("error", "结果不存在");
            return response()->json();
        }else{
            if(!in_array($action, $instance->getAllActions())){
                $request->session()->flash("error", '方法不存在');
                return response()->json();
            }

            if(in_array($action, $instance->getModalActions())){
                $controller = "\\".get_called_class();
                $model_name = Str::snake(class_basename($this->model));
                $view = "admin::".$action;
                if(view()->exists($model_name.".".$action)){
                    $view = $model_name.".".$action;
                }
                return view($view)->with('instance', $instance)->with('controller', $controller);;
            }
            $attributes = [];
            if(in_array($action, ['create', 'update'])){
                foreach($instance->getEditableColumns() as $column){
                    $name = $column->name;
                    if($instance->getExtendedName($name) != ""){
                        $name = $instance->getExtendedName($name);
                    }
                    $value = $request->input($name);
                    if($column->type == 'boolean'){
                        $value = $value == "on";
                    }
                    if($column->type == 'extended'){
                        if(!is_array($instance->getExtendedType($column->name))){
                            if($action == 'create'){
                                $value = null;
                            }else{
                                if ($request->hasFile($column->name)) {
                                    $request->file($column->name)->move(public_path('upload/').$instance->getFileStoragePath($column->name), $instance->getFileStorageName($column->name));
                                    $value = $instance->getFileUrl($column->name);
                                }else{
                                    $value = null;
                                }
                            }
                        }
                    }
                    if(!is_null($value)){
                        $attributes[$name] = $value;
                        $instance->{$name} = $value;
                    }
                }
                $validator = Validator::make($attributes, $instance->getRules(), $instance->getValidatorMessages());
                $validator->after(function($validator) use ($instance){
                    foreach ($instance->getCustomValidatorCallback() as $callback) {
                        if (!$callback()) {
                            //TODO
                        }
                    }
                });
                if($validator->fails()){
                    $request->session()->flash("error", $validator->errors()->first());
                    return response()->json();
                }
            }
            if($action == 'create'){
                $instance = $instance->$action($attributes);
                foreach($instance->getExtendedColumns() as $column){
                    if(!is_array($instance->getExtendedType($column->name))){
                        if ($request->hasFile($column->name)) {
                            $request->file($column->name)->move(public_path('upload/').$instance->getFileStoragePath($column->name), $instance->getFileStorageName($column->name));
                            $instance->{$column->name} = $instance->getFileUrl($column->name);
                        }
                    }
                }
                $instance->save();
            }else{
                $instance->$action();
            }

        }
        $request->session()->flash("success", '操作成功');
        return response()->json();
    }

    public function adminList(Request $request)
    {
        $data = $instance = $this->initInstance();
        $query = [];
        $filter = [];
        $columns = []; 
        foreach($instance->getAllColumns() as $column){
            $columns[] = $column->name;
        }
        foreach($request->input() as $key => $value){
            if(in_array($key, $instance->getExtended())){
                if($value != ""){
                    $data = $instance->filter($key, $value, $data);
                }
            }else{
                if(in_array($key, $columns) && $value != '*'){
                    $data = $data->where($key, 'LIKE', "%".$value."%");
                }
            }
            $query[$key] = $value;
        }
        $data = $data->orderBy('id', 'desc')->paginate(20);
        $controller = "\\".get_called_class();
        return view('admin::list', compact('data', 'instance', 'controller', 'query'));
    }

    public function adminShow($id)
    {
    }


    public function api(Request $request, $action, $id = null){
        $function = strtolower($request->method()).ucfirst(camel_case($action));
        if(method_exists($this, $function)){
            if(is_null($id)){
                return response()->json($this->$function());
            }else{
                return response()->json($this->$function($id));
            }
        }
        //
        $instance = $this->initInstance($id);
        if(is_null($instance)){
            return response()->json(['status' => false, 'msg' => "Item Not Found"]);
        }else{
            $validator = Validator::make($request->all(), $instance->getRules(), $instance->getValidatorMessages());
            $validator->after(function($validator) use ($instance){
                foreach ($instance->getCustomValidatorCallback() as $callback) {
                    if (!$callback()) {
                        //TODO
                    }
                }
            });
            if($validator->fails()){
                return response()->json(['status' => false, 'msg' => $validator->errors()]);
            }
            if(!in_array($action, $instance->getAllActions())){
                return response()->json(['status' => false, 'msg' => 'Action Not Found']);
            }

            $attributes = [];
            foreach($instance->getEditableColumns() as $column){
                $value = $request->input($column->name);
                if(!is_null($value)){
                    $attributes[$column->name] = $value;
                    $instance->{$column->name} = $value;
                }
            }
            if($action == 'create'){
                $result = ['status' => true, 'item' => $instance->$action($attributes)];
            }else{
                if($return = $instance->$action()){
                    $result = array_merge(['status' => true], $return);
                }
            }

        }
        return response()->json($result);
    }

    public function apiList()
    {
        $instance = $this->initInstance();
        return $instance->all();
    }

    public function apiShow($id){
        $instance = $this->initInstance($id);
        if(is_null($instance)){
            $result = ['status' => false, 'msg' => '你查找的对象不存在'];
        }else{
            $result = ['status' => true, 'item' => $instance->toArray()];
        }
        return response()->json($result);
    }

    protected function initInstance($id = null){
        $model = $this->model;
        if(is_null($id)){
            $instance = new $model([]);
        }else{
            $instance = $model::find($id);
        }
        return $instance;
    }

    public function batch($action){
    }


}
