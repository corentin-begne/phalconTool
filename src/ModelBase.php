<?
namespace Phalcon; 

use Phalcon\Mvc\Model\Behavior\Timestampable,
Phalcon\Mvc\Model\MetaData,
Phalcon\Builder\Form,
Phalcon\Annotations\Reader,
Phalcon\Annotations\Reflection,
Phalcon\Mvc\Model,
Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize;

/**
 * Base class of all models
 */
class ModelBase extends Model{


    /**
     * Get descriptions of all columns of the model
     * 
     * @param null|array $excludes=[] List of the columns to exclude
     * 
     * @return array Columns description
     */
    public static function getColumnsDescription(null|array $excludes=[]):array{
        $reader = new Reader();
        $parsing = $reader->parse(get_called_class());
        $reflector = new Reflection($parsing);
        $descriptions = [];
        foreach($reflector->getPropertiesAnnotations() as $annotations){
            foreach($annotations->getAnnotations() as $annotation){
                if($annotation->getName() !== 'Column'){
                    continue;
                }
                $args = $annotation->getArguments();
                if(!in_array($args['column'], $excludes)){
                    $descriptions[self::getPrefix().'_'.$args['column']] = $args;
                }
            }
        }
        return $descriptions;
    }

    /**
     * Get field type
     * 
     * @param string $field Name of the field
     * 
     * @return string|false Type of the field or false if not found
     */
    public static function getType(string $field):string|false{
        $model = get_called_class();
        foreach($model::getColumnsDescription() as $name => $option){
            if($name === $field){
                return $option['type'];
            }
        }
        return false;
    }

    /**
     * Get all relation by type
     * 
     * @param string $type Type of the relation
     * 
     * @return array Relations found
     */
    public static function returnRelations(string $type):array{
        $type = 'get'.(new Camelize())((new Uncamelize())($type));
        $relations=[];
        $model = get_called_class();
        $model = new $model();
        foreach($model->getModelsManager()->$type($model) as $relation){
            if($type === 'getBelongsTo'){
                $relations[$relation->getFields()] = [
                    'model' => $relation->getReferencedModel(),
                    'field' => $relation->getReferencedFields()
                ];
            } else {
                if(!isset($relations[$relation->getFields()])){
                     $relations[$relation->getFields()] = [];
                }
                 $relations[$relation->getFields()][] = [
                    'model' => $relation->getReferencedModel(),
                    'field' => $relation->getReferencedFields()
                ];
            }
        }
        return $relations;
    }

    /**
     * Set relations in \Phalcon\Builder\Form $relations static variable
     * 
     * @param string $type Type of the relation
     * 
     * @return void
     */
    public static function getRelations(string $type):void{
        Form::$relations = self::returnRelations($type);
    }

    /**
     * Check if current model has a hasOne relation with the target, if true add field in \Phalcon\Builder\Form $excludes static variable
     * 
     * @param string $target Name of the model to check with
     * 
     * @return bool Result of the check
     */
    public static function checkHasOne(string $target):bool{
        $model = get_called_class();
        $model = new $model();
        foreach($model->getModelsManager()->getHasOne($model) as $relation){
            if($relation->getReferencedModel() === $target){
                Form::$excludes[] = substr($relation->getReferencedFields(), strpos($relation->getReferencedFields(), '_')+1);
                return true;
            }
        }
        return false;
    }

    /**
     * Get referenced field(s) with a model having hasOne relation, return false if not found
     * 
     * @param string $model Name of the model to get whith
     * 
     * @return string|array|false Field name or list or false if not found
     */
    public static function getReferencedField(string $name):string|array|false{
        $model = get_called_class();
        $model = new $model();
        foreach($model->getModelsManager()->getHasOne($model) as $relation){
            if($relation->getReferencedModel() === $name){
                return $relation->getReferencedFields();
            }
        }
        return false;
    }

    /**
     * Get columns map
     * 
     * @return array Columns map
     */
    public static function getColumnsMap():array{
        $model = get_called_class();
        $model = new $model();
        return $model->getModelsMetaData()->getColumnMap($model);
    }

    /**
     * Merge params with columns description
     * 
     * @param array $params Params to add to columns description
     * 
     * @return array Result of the merge
     */
    public static function filterParams(array $params):array{
        $model = get_called_class();
        return array_intersect_key($params, $model::getColumnsDescription());
    }

    /**
     * Get erros from a PHQL query
     * 
     * @param null|array $errors=[] Errors to merge with
     * 
     * @return array Errors of the query
     */
    public function getErrors(null|array $errors=[]):array{
        foreach($this->getMessages() as $message){
            $errors[] = $message->getMessage();
        }
        return $errors;
    }

    /**
     * Get model prefix
     * 
     * @return string Model prefix
     */
    public static function getPrefix():string{
        $model = get_called_class();
        $prefix = '';
        foreach(explode('_', (new Uncamelize())($model)) as $name){
            $prefix .= $name[0].$name[1];
        }
        return $prefix;
    }

    /**
     * Get required columns
     * 
     * @return array List of all required columns
     */
    public static function getRequired():array{
        $columns = [];
        $prefix = self::getPrefix();
        foreach(self::getColumnsDescription() as $name => $column){
            if(!$column['nullable'] && !isset($column['default']) && (!isset($column['extra']) || $column['extra'] !== 'auto_increment') && !in_array($name, [$prefix.'_created_at', $prefix.'_updated_at'])){
                $columns[] = $name;
            }
        }
        return $columns;
    }

    /**
     * Get primary key
     * 
     * @return array Prmary key
     */
    public static function getPrimaryKey():string{
        $model = get_called_class();
        $model = new $model();
        return $model->getModelsMetaData()->getPrimaryKeyAttributes($model)[0];
    }

    /**
     * Get the field name mapped
     * 
     * @param string $field Field name to map
     * 
     * @return string Field name mapped
     */
    public static function getMapped(string $field):string{
        $model = get_called_class();
        $model = new $model();
        return $model->getModelsMetaData()->readColumnMapIndex($model, MetaData::MODELS_COLUMN_MAP)[$field];
    }

    /**
     * Add automatic values for fields created_at and updated_at on creation and update
     * 
     * @return void
     */
    public function initialize():void{
        $prefix = self::getPrefix();
        if(property_exists($this, $prefix.'_created_at')){
            $this->addBehavior(new Timestampable(
                [
                    'beforeCreate'  => [
                        'field'     => $prefix.'_created_at',
                        'format'    => 'Y-m-d H:i:s'
                    ]
                ]
            ));
        }
        if(property_exists($this, $prefix.'_updated_at')){
            $this->addBehavior(new Timestampable(
                [
                    'beforeCreate'  => [
                        'field'     => $prefix.'_updated_at',
                        'format'    => 'Y-m-d H:i:s'
                    ]
                ]
            ));
            $this->addBehavior(new Timestampable(
                [
                    'beforeUpdate'  => [
                        'field'     => $prefix.'_updated_at',
                        'format'    => 'Y-m-d H:i:s'
                    ]
                ]
            ));
        }
    }

}