<?

namespace Phalcon\Builder;
use Phalcon\DI, 
Phalcon\Mvc\Model\Relation, 
Phalcon\Text as Utils;

class Form extends \Phalcon\Tag
{
    public static $excludes = ['id', 'created_at', 'updated_at'];
    public static $relations = [];

    private static $types = [
        'phone'=>['pattern'=>'\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})'], 
        'password'=>['rel'=>'password'], 
        'email'=>'',
        'postal_code'=>['pattern'=>'^(([0-8][0-9])|(9[0-5]))[0-9]{3}$']
    ];

    public static function getFields($model, $excludes=[]){
        $excludes += self::$excludes;
        $model::getRelations('belongsTo');
        foreach($model::getColumnsDescription($excludes) as $name => $options){
            DI::getDefault()->getView()->partial('scrud/field', [
                'label'=>Form::getLabel($name), 
                'field'=>Form::getTag($name, $options)
            ]);
        }
    }

    public static function getLabel($name){
        $text = str_replace('_id', '', substr($name, strpos($name, '_')+1));
        return '<label class="form" for="'.$name.'">'.$text.'&nbsp;:&nbsp;</label>';
    }

    public static function getTag($name, $options, $restrictable=true){
        $option = [
            $name, 
            'class'=>'form'
        ];
        if($restrictable && !$options['isNull']){
            $option += ['required'=>''];
        }
        $type = 'text';
        switch($options['type']){
            case 'bigint':
            case 'int':
                if(isset($options['key']) && $options['key'] === 'MUL'){
                    $model = self::$relations[$name]['model'];
                    return self::select([
                        $name, 
                        $model::find(), 
                        'using' => [
                            self::$relations[$name]['field'], 
                            $model::getPrefix().'_name'
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
            case 'tinyint':
                $type = 'selectStatic';
                $args = [
                    ''=>'-',
                    '1'=>'Oui',
                    '0'=>'Non'
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