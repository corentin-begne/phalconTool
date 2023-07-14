<?
use Phalcon\Mvc\Model\Query\Builder,
Phalcon\ControllerBase,
Phalcon\Mvc\Dispatcher,
Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize;

/**
 * Provide actions to search, create, update and delete models data and autocompletion on field data search
 */
class ApiController extends ControllerBase{

    /**
     * Current request models
     * @var array
     */
    private $models = [];
    /**
     * Object to camelize strings
     * @var \Phalcon\Support\Helper\Str\Camelize
     */
    private Camelize $camelize;
    /**
     * Object to uncamelize strings
     * @var Phalcon\Support\Helper\Str\Uncamelize
     */
    private Uncamelize $uncamelize;

    /**
     * Check url models conformity
     * 
     * @param  \Phalcon\Mvc\Dispatcher $dispatcher Application dispatcher
     * 
     * @return void
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher):void{
        $this->camelize = new Camelize();
        $this->uncamelize = new Uncamelize();
        Rest::init();

        $this->models = [];
        $models = explode(' ', $dispatcher->getParam('model'));
        for($i=0; $i<count($models); $i++){
            $model = ($this->camelize)(($this->uncamelize)($models[$i]));
            if(!class_exists($model)){
                Rest::renderError("Model $model does not exists");
            }
            if($i>0){
                $className = $this->models[0];
                if(!$className::checkHasOne($model)){
                    Rest::renderError("Model $className hasOne relation to $model does not exists");
                }
            }
            $this->models[] = $model;            
        }
    }

    /**
     * Get models result filtered by conditions
     * 
     * @return void
     */
    public function findAction():void{
        $params = [];
        $conditions = [];
        foreach(Rest::$params['conditions'] as &$condition){
            $conditions[] = [
                $condition['name'].' '.(isset($condition['type']) ? $condition['type'] : '=').' :'.$condition['name'].':',
                [$condition['name'] => $condition['value']]
            ];
        }
        $model = $this->models[0];
        $params = [
            'models' => $model,
            'conditions' => $conditions
        ];
        if(isset(Rest::$params['fields'])){
            $params['columns'] = Rest::$params['fields'];
        }
        if(isset(Rest::$params['order'])){
            $params['order'] = Rest::$params['order'];
        }
        if(isset(Rest::$params['limit'])){
            $params['limit'] = Rest::$params['limit'];
        }
        if(isset(Rest::$params['offset'])){
            $params['offset'] = Rest::$params['offset'];
        }
        $primaryKey = $model::getMapped($model::getPrimaryKey());
        $builder = new Builder($params);
        for($i=1; $i<count($this->models); $i++){
            $name = $this->models[$i];
            $builder->leftJoin($name, $model::getReferencedField($name)." = $primaryKey");
        }
        try{
            Rest::renderSuccess($builder->getQuery()->execute()->toArray());
        } catch(PDOException $e){
            Rest::renderError($e->getMessage());
        }
    }

    /**
     * Get the type of models column
     * 
     * @return void
     */
    public function getTypeAction():void{
        Rest::checkParams(['field']);
        $model = $this->models[0];
        Rest::renderSuccess($model::getType(Rest::$params['field']));
    }

    /**
     * Autocomplete models field data search
     * 
     * @return void
     */
    public function completeAction():void{
        Rest::checkParams(['field', 'value']);
        $limit = isset(Rest::$params['limit']) ? (int)Rest::$params['limit'] : 10;
        $model = $this->models[0];
        $prefix = $model::getPrefix();
        $result = [];
        $rows =  $model::find([
            Rest::$params['field']." like '".Rest::$params['value']."%'",
            'columns'=>Rest::$params['field'].', '.$prefix.'_id',
            'limit' => $limit,
            'order' => Rest::$params['field']
        ]);
        foreach($rows as $row){
            $field = Rest::$params['field'];
            $result[] = "<div class='result' id='".$model::getMapped($model::getPrimaryKey())."'>".$row->$field.'</div>';
        }
        Rest::renderSuccess($result);
    }

    /**
     * Create models entry
     * 
     * @return void
     */
    public function createAction():void{
        $result = [];
        $refModel = $this->models[0];
        $primaryKey = $refModel::getMapped($refModel::getPrimaryKey());
        $refValue = '';
        try{
            for($i=0; $i<count($this->models); $i++){            
                $model = $this->models[$i]; 
                if($i>0){
                    Rest::$params[$refModel::getReferencedField($model)] = $refValue;
                }
                Rest::checkParams($model::getRequired()); 
                $params = $model::filterParams(Rest::$params);                
                $model = new $model($params);
                if(!$model->create()){
                    Rest::renderError($model->getErrors());
                }
                if($i===0){
                    $refValue = $model->$primaryKey;
                }
                $result[$this->models[$i]] = $model->toArray();
            }        
            Rest::renderSuccess($result);
        } catch(PDOException $e){
            Rest::renderError($e->getMessage());
        }
    }

    /**
     * Update models entry
     * 
     * @return void
     */
    public function updateAction():void{
        $refModel = $this->models[0];
        $primaryKey = $refModel::getPrimaryKey();
        $refValue = $this->request->get($primaryKey);
        if(!isset($refValue)){
            Rest::renderError('Missing mandatory param !');            
        }
        try{
            for($i=0; $i<count($this->models); $i++){  
                $model = $this->models[$i];          
                if($i === 0){
                    $field = $model::getMapped($model::getPrimaryKey());
                } else {
                    $field = $refModel::getReferencedField($model);
                }            
                $fn = 'findFirstBy'.($this->camelize)($field);
                $row = $model::$fn($refValue);
                $params = $model::filterParams(Rest::$params);
                if($row === null){
                    $row = new $model();
                    $row->$field = $refValue;
                }
                
                $row->assign($params);
                if(!$row->save()){
                    Rest::renderError($row->getErrors());
                }
            }
        } catch(PDOException $e){
            Rest::renderError($e->getMessage());
        }
        Rest::renderSuccess();
    }

    /**
     * Delete models entry
     * 
     * @return void
     */
    public function deleteAction():void{
        $model = $this->models[0];
        $primaryKey = $model::getMapped($model::getPrimaryKey());
        try{
            $rows = $model::find("$primaryKey IN (".implode(',', Rest::$params['ids']).")");
            if(!$rows->delete()){
                Rest::renderError($rows[0]->getErrors());
            }
        } catch(PDOException $e){
            Rest::renderError($e->getMessage());
        }
        Rest::renderSuccess();
    }

}