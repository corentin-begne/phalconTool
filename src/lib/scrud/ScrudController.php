<?
use Phalcon\Text as Utils,
Phalcon\Tag;
class ScrudController extends Phalcon\ControllerBase{
    
    public $excludes = [];

    public function initialize()
    {               
        if($this->dispatcher->getControllerName() === 'scrud'){
            $this->view->setViewsDir($this->config->application->rootDir.'vendor/v-cult/phalcon/src/lib/scrud/views/');
            $this->view->setLayout('scrud');
        }
        if($this->router->getActionName() === 'index'){
            $this->indexAction();
            return false;
        }
        $cleanController = str_replace('custom_', '', $this->dispatcher->getControllerName());
        $currentPath = ($this->dispatcher->getActionName() === 'index') ? $cleanController : $cleanController.'/'.$this->dispatcher->getActionName(); 
        $this->assets->set('libjs', new Phalcon\Assets\Collection());
        $this->assets->collection('libjs')
        ->setPrefix('/lib/'.$this->dispatcher->getControllerName().'/public/js/')
        ->addJs('lib/jquery.min.js')  
        ->addJs('lib/jquery.percentageloader-0.2.js')
        ->addJs('model/action.js')
        ->addJs('model/manager.js')
        ->addJs('helper/action.js')
        ->addJs('helper/form.js')
        ->addJs('helper/js.js')
        ->addJs($currentPath.'/manager.js')
        ->addJs($currentPath.'/main.js');              
        $this->models = [];
        $this->limit = 20;
        $models = explode(' ', $this->dispatcher->getParam('model'));
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
        $this->view->manager = ucfirst($this->router->getActionName()).ucfirst($cleanController).'Manager';
        $this->view->models = $this->models;
        $this->view->actionModel = $this->dispatcher->getParam('model');
    }

    public function indexAction(){
        $this->assets->set('libjs', new Phalcon\Assets\Collection());
        $models = [];
        foreach($this->db->listTables($this->config[ENV]->database->dbname) as $model){
            if(!isset($models[$model[0]])){
                $models[$model[0]] = [];
            }
            $models[$model[0]][] = $model;
        }
        ksort($models);
        $this->view->models = $models;
    }

    public function listAction(){
        $fields = $this->request->get('fields');
        $filters = $this->request->get('filters');
        $currentPage = $this->request->get('currentPage');
        $order = $this->request->get('order');
        $conditions = [];
        if(!isset($fields)){
            die($this->flash->error("Missing fields"));
        }
        if(!isset($currentPage)){
            die($this->flash->error("Missing currentPage"));
        }
        if(!isset($order)){
            die($this->flash->error("Missing order"));
        }
        if(isset($filters)){
            foreach($filters as $name => &$filterTab){
                foreach($filterTab as &$filter){
                    if(strpos($filter['type'], '%') !== false){
                        if(strpos($filter['type'], '%like') !== false){
                            $filter['value'] = '%'.$filter['value'];
                        }
                        if(strpos($filter['type'], 'like%') !== false){
                            $filter['value'] .= '%';
                        }
                        $filter['type'] = str_replace('%', '', $filter['type']);
                    }
                    $conditions[] = [
                        "$name ".$filter['type']." :$name:",
                        [$name=>$filter['value']]
                    ];
                }
            }
        }
        $model = $this->models[0];
        
        $primaryKey = $model::getMapped($model::getPrimaryKey());
        $params = [
            'models' => $model,
            'columns' => $fields,
            'hydration' => \Phalcon\Mvc\Model\Resultset::TYPE_RESULT_FULL,
            'offset' => ((int)$currentPage-1)*$this->limit,
            'limit' => $this->limit,
            'conditions' => $conditions,
            'order' => $order
        ];
        $this->view->rows = $this->getRows($params);     
        unset($params['limit']);
        unset($params['offset']);
        $params['columns'] = 'count(*) as nb';
        $this->view->nbPage = ceil((int)$this->getRows($params)->toArray()[0]['nb']/$this->limit);
        $this->view->setRenderLevel(Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $this->view->primaryKey = $primaryKey;
        $this->view->includes = $fields;
        
    }

    private function getRows(&$params){
        $model = $this->models[0];
        $primaryKey = $model::getMapped($model::getPrimaryKey());
        $builder = new Phalcon\Mvc\Model\Query\Builder($params);
        for($i=1; $i<count($this->models); $i++){
            $name = $this->models[$i];
            $builder->leftJoin($name, $model::getReferencedField($name)." = $primaryKey");
        }
        return $builder->getQuery()->execute();
    }

    public function searchAction(){
        $this->assets->collection('libjs')
        ->addJs('helper/autocompletion.js')
        ->addJs('helper/pagination.js')
        ->addJs('helper/popup.js')
        ->addJs('lib/jquery-ui.min.js');
        $this->view->types = [
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
        ];  
        $this->view->sort = [
            'asc' => 'Croissant',
            'desc' => 'Décroissant'
        ];
        $fields = [];
        foreach($this->models as $model){
            $fields += [$model => $model::getColumnsMap()];
        }
        
        $model = $this->models[0];
        
        $primaryKey = $model::getMapped($model::getPrimaryKey());
        $this->view->rows = $model::find([
            'offset' => 0,
            'limit' => $this->limit,
            'order' => $primaryKey
        ]);
        $model::getRelations('belongsTo');
        $this->view->fields = [''=>'-']+$fields;
        $this->view->primaryKey = $primaryKey;        
        $this->view->nbPage = ceil($model::count()/$this->limit);    
        Tag::setDefault('fieldSort', $primaryKey);
        Tag::setDefault('sort', 'asc');
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
                continue;
               // die($this->flash->error("$primaryKey $refValue not Found !"));
            }
            foreach($row->toArray() as $name => $value){
                Tag::setDefault($name, $value);
            }
        }
        Tag::setDefault($primaryKey, $refValue);
        $this->view->primaryKey= $primaryKey;
    }

    public function createAction(){
    }

    public function updateAction(){

    }

    public function deleteAction(){

    }

}