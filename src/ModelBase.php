<?
use Phalcon\Mvc\Model,
Phalcon\Annotations\Adapter\Memory,
Phalcon\Mvc\Model\Behavior\Timestampable,
Phalcon\Mvc\Model\MetaData,
Phalcon\Builder\Form,
Phalcon\Text as Utils,
Phalcon\DI;

class ModelBase extends Model{

    public static function getColumnsDescription($excludes=[]){
        $reader = new Memory();
        $reflector = $reader->get(get_called_class());
        $descriptions = [];
        foreach($reflector->getPropertiesAnnotations() as $annotations){
            foreach($annotations->getAnnotations() as $annotation){
                if(!in_array(substr($annotation->getName(), strpos($annotation->getName(), '_')+1), $excludes)){
                    $descriptions[$annotation->getName()] = $annotation->getArguments()[0];
                }
            }
        }
        return $descriptions;
    }

    public static function getType($field){
        $model = get_called_class();
        foreach($model::getColumnsDescription() as $name => $option){
            if($name === $field){
                return $option['type'];
            }
        }
    }

    public static function getRelations($type){
        $model = get_called_class();
        $type = 'get'.Utils::camelize(Utils::uncamelize($type));
        Form::$relations=[];
        foreach(DI::getDefault()->getModelsManager()->$type(new $model()) as $relation){
            Form::$relations[$relation->getFields()] = [
                'model' => $relation->getReferencedModel(),
                'field' => $relation->getReferencedFields()
            ];
        }
    }

    public static function checkHasOne($target){
        $model = get_called_class();
        foreach(DI::getDefault()->getModelsManager()->getHasOne(new $model()) as $relation){
            if($relation->getReferencedModel() === $target){
                Form::$excludes[] = substr($relation->getReferencedFields(), strpos($relation->getReferencedFields(), '_')+1);
                return true;
            }
        }
        return false;
    }

    public static function getReferencedField($model){
        $className = get_called_class();
        foreach(DI::getDefault()->getModelsManager()->getHasOne(new $className()) as $relation){
            if($relation->getReferencedModel() === $model){
                return $relation->getReferencedFields();
            }
        }
    }

    public static function getColumnsMap(){
        $className = get_called_class();
        return DI::getDefault()->getModelsMetadata()->getReverseColumnMap(new $className());
    }

    public static function filterParams($params){
        $model = get_called_class();
        return array_intersect_key($params, $model::getColumnsDescription());
    }

    public function getErrors($errors=[]){
        foreach($this->getMessages() as $message){
            $errors[] = $message->getMessage();
        }
        return $errors;
    }

    public static function getPrefix(){
        $model = get_called_class();
        $prefix = '';
        foreach(explode('_', Utils::uncamelize($model)) as $name){
            $prefix .= $name[0].$name[1];
        }
        return $prefix;
    }

    public static function getRequired(){
        $columns = [];
        $prefix = self::getPrefix();
        foreach(self::getColumnsDescription() as $name => $column){
            if(!$column['isNull'] && !isset($column['default']) && (!isset($column['extra']) || $column['extra'] !== 'auto_increment') && !in_array($name, [$prefix.'_created_at', $prefix.'_updated_at'])){
                $columns[] = $name;
            }
        }
        return $columns;
    }

    public static function getPrimaryKey(){
        $className = get_called_class();
        return DI::getDefault()->getModelsMetadata()->getPrimaryKeyAttributes(new $className())[0];
    }

    public static function getMapped($field){
        $className = get_called_class();
        return DI::getDefault()->getModelsMetadata()->readColumnMapIndex(new $className(), MetaData::MODELS_COLUMN_MAP)[$field];
    }

    public function initialize(){
        $prefix = self::getPrefix();
        if(property_exists($this, 'created_at')){
            $this->addBehavior(new Timestampable(
                [
                    'beforeValidationOnCreate'  => [
                        'field'     => $prefix.'_created_at',
                        'format'    => 'Y-m-d H:i:s'
                    ]
                ]
            ));
        }
        if(property_exists($this, 'updated_at')){
            $this->addBehavior(new Timestampable(
                [
                    'beforeValidationOnCreate'  => [
                        'field'     => $prefix.'_updated_at',
                        'format'    => 'Y-m-d H:i:s'
                    ]
                ]
            ));
            $this->addBehavior(new Timestampable(
                [
                    'beforeValidationOnUpdate'  => [
                        'field'     => $prefix.'_updated_at',
                        'format'    => 'Y-m-d H:i:s'
                    ]
                ]
            ));
        }
    }

}