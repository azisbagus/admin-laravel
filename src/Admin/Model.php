<?php
namespace Friparia\Admin;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

abstract class Model extends LaravelModel
{

    protected $_name;
    /**
     * List page configurations
     */
    protected $_unlistable = [];
    protected $_filterable = [];
    protected $_switchable = [];


    /**
     * Create or edit page  configurations
     */
    protected $_uncreatable = [];
    protected $_uneditable = [];


    protected $_unshowable = [];


    /**
     * Model fields defenition
     */
    protected $_fields = [];


    /**
     * Model actions
     * Array of actions
     */

    protected $_actions = [];

    /**
     * Laravel extensions
     */
    protected $guarded = [];

    /**
     *
     * protected $title = "商户";
    protected $unlistable = ['created_at', 'updated_at', 'deleted_at', 'card'];
    protected $filterable = ['type', 'province', 'city', 'district'];
    protected $searchable = ['name'];
    protected $extended = ['province', 'city', 'district', 'card'];
    protected $switchable = ['is_top', 'is_cooper'];
    protected $order = ['name', 'type', 'province', 'city', 'district', 'is_top', 'is_cooper'];
    public $timestamps = false;
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
        'switch_is_top' => [
            'type' => 'extend',
        ],
        'switch_is_cooper' => [
            'type' => 'extend',
        ],
    ];
    protected function construct(){
        ///////
        //$this->fields->timestamps(); 
        //$this->fields->softDeletes();
        ///////
        $this->fields->string('name')->description('商户名称');
        $this->fields->enum('type',['sport','entertainment','travel','food','other'])->description('商户类型')->values([
            'sport' => '运动',
            'entertainment' => '娱乐',
            'travel' => '旅行',
            'food' => '美食',
            'other' => '其他',
        ]);
        $this->fields->boolean('is_top')->description('置顶');
        $this->fields->boolean('is_cooper')->description('合作');
        $this->fields->relation('district')->belongsTo('App\Models\District')->description("区县");
    }
     */

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->configure();
        $this->_name = Str::snake(class_basename($this));
        // $this->fields = new Blueprint($this->getTable());
    }

    protected function configure(){
    }

    protected function addField($type, $name){
        $field = new Field($type, $name); 
        $fields = $this->_fields;
        $fields[] = $field;
        $this->_fields = $fields;
        return $field;
    }

    protected function addAction($type, $name){
        $action = new Action($type, $name); 
        $actions = $this->_actions;
        $actions[] = $action;
        $this->_actions = $actions;
        return $action;
    }

    public function createMigrationFile(){
        $creator = new MigrationCreator($this);
        $path = database_path().'/migrations';
        $creator->create($this->_name, $path, $this->getTable());
    }


    public function getFields()
    {
        return $this->_fields;
    }

    /**
    public function getRelations()
    {
        return $this->fields->getRelations();
    }


    public function getManyToManyRelation(){
        $relations = [];
        foreach($this->getRelations() as $relation){
            if($relation['type'] == Relation::MANY_TO_MANY){
                $relations[] = $relation;
            }
        }
        return $relations;
    }

    //need reconstruct
    public function getColumns1()
    {
        $cols = $this->fields->getColumns();
        $relatedKey = [];
        foreach($this->getRelations() as $relation){
            if($relation->type == Relation::BELONGS_TO){
                $relatedKey[] = $relation->foreignKey;
            }
        }
        $columns = [];
        foreach($cols as $column){
            if(!in_array($column->name, $relatedKey)){
                $columns[] = $column;
            }
        }

        foreach($this->extended as $extended){
            if(!$description = $this->getColumnDescription($extended)){
                $description = $extended;
            }
            $columns[] = new Fluent([
                'name' => $extended,
                'type' => 'extended',
                'description' => $description
            ]);
        }
        return $columns;
    }
    public function getAllColumns()
    {
        return $this->fields->getColumns();
    }

    public function getColumnDescription($column){
        $description = $this->getColumn($column)->description;
        if(isset($description)){
            return $description;
        }
        return $column;
    }

    public function getListableColumns(){
        $columns = [];

        $this->unlistable[] = 'id';
        foreach($this->getColumns() as $column){
            if(!in_array($column['name'], $this->unlistable)){
                $columns[] = $column;
            }
        }
        return $columns;
    }

    public function getExtendedType($name){
        return false;
    }

    public function getExtendedName($name){
        return "";
    }

    public function getExtendedColumns(){
        $columns = [];
        foreach($this->extended as $extended){
            if(!$description = $this->getColumnDescription($extended)){
                $description = $extended;
            }
            $columns[] = new Fluent([
                'name' => $extended,
                'type' => 'extended',
                'description' => $description
            ]);
        }
        return $columns;

    }

    public function getFilterableColumns(){
        $columns = [];
        foreach($this->getColumns() as $column){
            if(in_array($column['name'], $this->filterable)){
                $columns[] = $column;
            }
        }
        return $columns;
    }

    public function getSearchableColumns(){
        $columns = [];
        foreach($this->getColumns() as $column){
            if(in_array($column['name'], $this->searchable)){
                $columns[] = $column;
            }
        }
        return $columns;
    }

    public function getShowableColumns(){}

    public function getEditableColumns(){
        $columns = [];
        foreach($this->getColumns() as $column){
            $name = $column->name;
            if(!in_array($name, $this->uneditable) && $name != 'id'){
                $columns[] = $column;
            }
        }
        return $columns;
    }

    protected function getColumn($name){
        foreach($this->getColumns() as $column){
            if($column->get('name') == $name){
                return $column;
            }
        }
    }

    public function getValue($name){
        $column = $this->getColumn($name);
        if($column->type == 'enum'){
            return $column->values[$this->$name];
        }
        if($column->type == 'boolean'){
            return ['0' => '否', '1' => '是'][$this->$name];
        }
        return $this->$name;
    }

    public function getRawValue($name){
        return $this->$name;
    }

    public function getAllActions(){
        return array_merge(['create', 'update', 'delete'], array_keys($this->actions));
    }

    public function getEachActions(){
        $actions = [];
        foreach($this->actions as $action => $value){
            if(isset($value['each'])){
                if($value['each']){
                    $actions[$action] = $value;
                }
            }elseif($value['type'] != 'extend'){
                $actions[$action] = $value;
            }
        }
        return $actions;
    }

    public function getModalActions(){
        $actions = [];
        foreach($this->actions as $action => $value){
            if(isset($value['type']) && $value['type'] == "modal"){
                $actions[] = $action;
            }
        }
        return $actions;
    }

    public function getLeftActions(){
        $actions = [];
        foreach($this->actions as $action => $value){
            if(isset($value['type']) && $value['type'] == "left"){
                $actions[$action] = $value;
            }
        }
        return $actions;
    }
    public function getBatchActions(){
        return [];
    }

    public function getSingleActions(){
        $actions = [];
        foreach($this->actions as $action => $value){
            if(isset($value['single'])){
                if($value['single']){
                    $actions[$action] = $value;
                }
            }
        }
        return $actions;
    }

    public function getValueGroups($column){
        $data = [];
        if(!in_array($column, $this->extended)){
            $type = $this->getColumn($column)->type;
            foreach(self::all()->groupBy($column) as $key => $item){
                if($type == 'enum'){
                    $value = $this->getColumn($column)->values[$key];
                }elseif($type == 'boolean'){
                    $value = ['0' => '否', '1' => '是'][$key];
                }else{
                    $value = $item[0][$column];
                }
                $data[$key] = $value;
            }
        }else{
            $data = $this->getExtendedValueGroups($column);
        }
        return $data;
    }

    public function getExtendedValueGroups($column){
        return [];
    }

    public function canFilterColumn($column){
        if(in_array($column, $this->filterable)){
            return true;
        }
        return false;
    }

    public function getExtended(){
        return $this->extended;
    }

    public function filter($key, $value, $data){
        return $data;
    }

    public function canListColumn($column){}

    public function canShowColumn($column){}

    public function canCreateColumn($column){}

    public function canEditColumn($column){}

    public function isSwitchable($column){
        if(in_array($column, $this->switchable)){
            return true;
        }
        return false;
    }

    static public function search($q){}

    public function getRules(){
        return [];
    }

    public function getValidatorMessages(){
        return [];
    }

    public function getCustomValidatorCallback(){
        return [];
    }


    public function newFromBuilder($attributes = [], $connection = null){
        $model = parent::newFromBuilder($attributes, $connection);
        foreach($this->fields->getRelations() as $key => $relation) {
            if ($relation['type'] == Relation::BELONGS_TO) {
                $relation = $model->belongsTo($relation['related'], $relation['foreignKey'], $relation['otherKey']);
                $model->$key = $relation->getResults();
                $model->setRelation($key, $relation);
            }else if ($relation['type'] == Relation::MANY_TO_MANY || $relation['type'] == Relation::BELONGS_TO_MANY) {
                $relation = $model->belongsToMany($relation['related'], $relation['table'], $relation['foreignKey'], $relation['otherKey']);
                $model->$key = $relation->getResults();
                $model->setRelation($key, $relation);
            }else if ($relation['type'] == Relation::HAS_MANY){
                $relation = $model->hasMany($relation['related'], $relation['foreignKey'], $relation['localKey']);
                $model->$key = $relation->getResults();
                $model->setRelation($key, $relation);
            }else if ($relation['type'] == Relation::HAS_ONE){
                $relation = $model->hasOne($relation['related'], $relation['foreignKey'], $relation['localKey']);
                $model->$key = $relation->getResults();
                $model->setRelation($key, $relation);
            }
        }
        return $model;
    }


    public function getDirty(){
        $dirty = parent::getDirty();
        foreach(array_keys($this->relations) as $relation){
            unset($dirty[$relation]);
        }

        return $dirty;
    }

    public function toArray(){
         $attributes = $this->attributesToArray();
         $arr = array_merge($attributes, $this->relationsToArray());
         foreach($this->relations as $key => $relation){
             unset($arr[$key]);
         }
         return $arr;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getFileStoragePath($name){
        return 'img/'.$name.'';
    }

    public function getFileStorageName($name){
        return $this->id;
    }

    public function getFileUrl($name){
        return asset('upload/'.$this->getFileStoragePath($name).'/'.$this->getFileStorageName($name));
    }
     */

}
