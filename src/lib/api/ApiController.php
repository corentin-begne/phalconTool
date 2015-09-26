<?
use Phalcon\Text as Utils,
Phalcon\Tools\Rest;
class ApiController extends Phalcon\ControllerBase{

    public $model;

    public function beforeExecuteRoute($dispatcher){
        Rest::init();

        $this->models = [];
        $models = explode(' ', $dispatcher->getParam('model'));
        for($i=0; $i<count($models); $i++){
            $model = Utils::camelize(Utils::uncamelize($models[$i]));            
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

    public function findAction(){
        $query = Criteria::fromInput($this->di, $this->model, Rest::$params);
        $className = $this->model;
        var_dump($className::find($query->getParams()));
    }

    public function getTypeAction(){
        Rest::checkParams(['field']);
        $model = $this->models[0];
        Rest::renderSuccess($model::getType(Rest::$params['field']));
    }

    public function completeAction(){
        Rest::checkParams(['field', 'value']);
        $limit = isset(Rest::$params['limit']) ? (int)Rest::$params['limit'] : 10;
        $model = $this->models[0];
        $result = [];
        $rows =  $model::find([
            Rest::$params['field']." like '".Rest::$params['value']."%'",
            'limit' => $limit,
            'order' => Rest::$params['field']
        ]);
        foreach($rows as $row){
            $field = Rest::$params['field'];
            $result[] = "<div class='result' id='".$model::getMapped($model::getPrimaryKey())."'>".$row->$field.'</div>';
        }
        Rest::renderSuccess($result);
    }

    public function createAction(){
        $result = [];
        $refModel = $this->models[0];
        $primaryKey = $refModel::getMapped($refModel::getPrimaryKey());
        try{
            for($i=0; $i<count($this->models); $i++){            
                $model = $this->models[$i]; 
                if($i>0){
                    Rest::$params[$refModel::getReferencedField($model)] = $refValue;
                }
                Rest::checkParams($model::getRequired()); 
                $params = $model::filterParams(Rest::$params);                
                $model = new $model();
                if(!$model->create($params)){
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

    public function updateAction(){
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
                $fn = 'findFirstBy'.Utils::camelize($field);
                $row = $model::$fn($refValue);
                if(!$row){
                    die($this->flash->error("$primaryKey $refValue not Found !"));
                }
                $params = $model::filterParams(Rest::$params);
                $row->assign($params);
                if(!$row->save()){
                    Rest::renderError($model->getErrors());
                }
            }
        } catch(PDOException $e){
            Rest::renderError($e->getMessage());
        }
        Rest::renderSuccess();
    }

    public function deleteAction(){

    }

}