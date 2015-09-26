<?
use Phalcon\Text as Utils,
Phalcon\Tag;
class ScrudController extends Phalcon\ControllerBase{
    
    public $excludes = [];

    public function beforeExecuteRoute($dispatcher)
    {
        $this->assets->collection('libjs')
        ->addJs('lib/jquery.percentageloader-0.2.js')
        ->addJs('model/action.js')
        ->addJs('model/manager.js')
        ->addJs('helper/action.js')
        ->addJs('helper/manager.js')
        ->addJs('helper/form.js')
        ->addJs('helper/js.js');
        $this->view->setViewDir($this->config->application->viewsDir.'lib/views/');
        $this->view->setLayout('scrud');
        $this->models = [];
        $models = explode(' ', $dispatcher->getParam('model'));
        for($i=0; $i<count($models); $i++){
            $models[$i] = Utils::camelize(Utils::uncamelize($models[$i]));
            $model = $models[$i];            
            if(!class_exists($model)){
                die($this->flash->error("Model $model does not exists"));
            }
            if($i>0){
                $className = $this->models[0];
                if(!$className::checkHasOne($model)){
                    die($this->flash->error("Model $className hasOne relation to $model does not exists"));
                }
            }
            $this->models[] = $model;            
        }
        $this->view->setVar('models', $this->models);
    }

    public function indexAction(){

    }

    public function searchAction(){
        $this->assets->collection('libjs')->addJs('helper/autocompletion.js');
        $this->view->setVar('types', [
            'numeric' => [
                '=' => '=',
                '!=' => '!=',
                '>=' => '>=',
                '<=' => '<='
            ],
            'text' => [
                'like' => 'like',
                'not like' => 'not like',
                'like%' => 'like%',
                '%like' => '%like',
                '%like%' => '%like%',
                'not like%' => 'not like%',
                'not %like' => 'not %like',
                'not %like%' => 'not %like%',
            ]
        ]);  
        $fields = [];
        foreach($this->models as $model){
            $fields += [$model => $model::getColumnsMap()];
        }
        
        $model = $this->models[0];
        /*
        $rows = $this->modelsManager->createQuery("");*/
        $rows = $model::find([
            'offset' => $offset = (Rest::$currentPage-1)*Rest::$limit,
            'limit' => Rest::$limit,
            'order' => $model::getMapped($model::getPrimaryKey())
        ]);
        $this->view->setVar('fields', [''=>'-']+$fields);
        $this->view->setVar('rows', $rows);
    }

    public function readAction(){
        $refModel = $this->models[0];
        $primaryKey = $refModel::getPrimaryKey();
        $refValue = $this->request->get($primaryKey);
        if(!isset($refValue)){
            die($this->flash->error("Missing mandatory param !"));
        }
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
            foreach($row->toArray() as $name => $value){
                Tag::setDefault($name, $value);
            }
        }
        Tag::setDefault($primaryKey, $refValue);
        $this->view->setVar('primaryKey', $primaryKey);
    }

    public function createAction(){
    }

    public function updateAction(){

    }

    public function deleteAction(){

    }

}