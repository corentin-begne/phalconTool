<?

namespace Phalcon\Builder;
use Phalcon\Di\Di,
Phalcon\Tag;

/**
 * Manage forms elements based on models relation et fields types
 */
class Form extends Tag
{
    /**
     * List of fields to exclude
     * @var array
     */
    public static array $excludes = ['id', 'created_at', 'updated_at'];
    /**
     * Model relations
     * @var array
     */
    public static array $relations = [];
    /**
     * Fields types with options
     * @var array
     */
    private static array $types = [
        'phone'=>['pattern'=>'\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})'], 
        'password'=>['rel'=>'password'], 
        'email'=>'',
        'postal_code'=>['pattern'=>'^(([0-8][0-9])|(9[0-5]))[0-9]{3}$']
    ];

    /**
     * Includes all fields elements partial based on model
     * 
     * @param string $model Model name
     * @param null|array $excludes=[] Fields to exclude
     * 
     * @return void
     */
    public static function getFields(string $model, null|array $excludes=[]):void{
        $excludes += self::$excludes;
        $model::getRelations('belongsTo');
        foreach($model::getColumnsDescription($excludes) as $name => $options){
            DI::getDefault()->get('view')->partial('/'.DI::getDefault()->get('router')->getControllerName().'/field', [
                'label'=>Form::getLabel($name), 
                'field'=>Form::getTag($name, $options)
            ]);
        }
    }

    /**
     * Get display data of a field from a Resultset
     * 
     * @param mixed $row Resultset of the query
     * @param string $model Model name
     * @param string $id Name of the field
     * 
     * @return mixed Value to display
     */
    public static function getDisplayValue(mixed $row, string $model, string $id):mixed{ 
       $relations = $model::returnRelations('belongsTo');
       if($model::getType($id) === 'tinyint'){
            if(!isset($row->$id)){
                return '';
            } else {
                return ((int)$row->$id === 0) ? 'No' : 'Yes';
            }
        } else if(isset($relations[$id])){
            $modelName = $relations[$id]['model'];
            $fieldName = $modelName::getPrefix().'_name';
            $modelObj = new $modelName();
            if($modelObj::getType($fieldName) !== null){
                $fn = 'findFirstBy'.ucfirst($relations[$id]['field']);
                return $modelName::$fn($row->$id)->$fieldName;
            } else {
                return $row->$id;
            }
        } else {
            return $row->$id;
        }
    }

    /**
     * Get field label
     * 
     * @param $name Label field name
     * 
     * @return string Label to display
     */
    public static function getLabel(string $name):string{
        $text = str_replace('_id', '', substr($name, strpos($name, '_')+1));
        return '<label class="form" for="'.$name.'">'.$text.'&nbsp;:&nbsp;</label>';
    }

    /**
     * Get field element tag from model
     * 
     * @param string $name Name of the field
     * @param array $options Options of the field
     * @param null|bool $restrictable=true Set field required or not
     * 
     * @return string Html tag of the field
     */
    public static function getTag(string $name, array $options, null|bool $restrictable=true):string{
        $option = [
            $name, 
            'class'=>'form'
        ];
        if($restrictable && !$options['nullable']){
            $option += ['required'=>''];
        }
        $type = 'text';        
        switch($options['mtype']){
            case 'bigint':
            case 'int':
                if(isset($options['key']) && $options['key'] === 'MUL'){
                    $model = self::$relations[$name]['model'];                    
                    $modelObj = new $model();
                    $modelNameMap = $model::getPrefix().'_name';
                    if($modelObj::getType($modelNameMap) === null){
                        $modelNameMap = $name;
                    }
                    return self::select([
                        $name, 
                        $model::find(), 
                        'using' => [
                            self::$relations[$name]['field'], 
                            $modelNameMap
                        ],
                        'useEmpty' => true,
                        'emptyText' => '-',
                    ]);
                } else {
                    $type = 'numeric';
                    $option += [
                        'min'=>0, 
                        'max'=>((int)str_pad('1', $options['length'], '0')-1),
                        'maxlength'=>$options['length'],
                        'size'=>$options['length']
                    ];
                }    
                break;
            case 'double':
            case 'float':
                $type = 'numeric';
                $option += [
                    'min'=>0, 
                    'max'=>((int)str_pad('1', $options['length'], '0')-1),
                    'maxlength'=>$options['length'],
                    'size'=>$options['length'],
                    'step'=>'0.01'
                ];
                break;
            case 'varchar':
                $option += [
                    'maxlength'=>$options['length'],
                    'size'=>$options['length'],
                ];
                foreach(self::$types as $pattern => $data){
                    if(preg_match("/_$pattern/", $name)){
                        if($pattern === 'email'){
                            $type = 'email';
                        } else {
                            if($pattern == 'phone'){
                                $type = 'tel';
                            }
                            $option += $data;
                        }
                        break;
                    }
                }
                break;  
            case 'set':
            case 'enum':
                $type = 'selectStatic';
                $list = explode(',', $options['length']);
                $args = [];
                if($options['type'] === 'set'){
                    $args['']='';
                }
                foreach($list as $name){
                    $args[$name] = $name;
                }
                break;
            case 'tinyint':
                $type = 'selectStatic';
                $args = [
                    ''=>'-',
                    '1'=>'Yes',
                    '0'=>'No'
                ];
                break;  
            case 'text':
                $type = 'textarea';
                break;
            case 'blob': 
            case 'longblob':
                $type = 'file';
                break;
            case 'datetime': 
            case 'timestamp':
                $type = 'dateTime';
                break;
            case 'date':
                $type = 'date';
                break; 
        }       
        if(isset($args)){
            return self::$type($option, $args);
        } else {
            if($type !== 'textarea'){
                $type .= 'Field';
            }
            return self::$type($option);
        }
    }
}