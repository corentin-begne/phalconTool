<?
namespace Phalcon;
use Phalcon\Forms\Form,
Phalcon\DI,
Phalcon\Forms\Element\Email,
Phalcon\Forms\Element\Text,
Phalcon\Forms\Element\Password,
Phalcon\Forms\Element\Select,
Phalcon\Forms\Element\SelectStatic,
Phalcon\Forms\Element\Check,
Phalcon\Forms\Element\TextArea,
Phalcon\Forms\Element\Hidden,
Phalcon\Forms\Element\File,
Phalcon\Forms\Element\Date,
Phalcon\Text as Utils,
Phalcon\Forms\Element\Numeric;

class FormBase extends Form
{
    protected $patterns = [
        'phone'=>['pattern'=>'\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})'],
        'postal_code'=>['pattern'=>'^(([0-8][0-9])|(9[0-5]))[0-9]{3}$'],
        'password'=>[], 
        'email'=>[],
    ];

    public function initialize($entity, $data=[]){
    	$t = $data['t'];
    	$model = get_class($entity);
        $relations = $model::returnRelations('belongsTo');
        $primary = $model::getMapped($model::getPrimaryKey());
        foreach($model::getColumnsDescription() as $name => $options){
        	if(strpos($name, 'created') !== false || strpos($name, 'updated') !== false){
        		continue;
        	}
        	if($primary === $name && !isset($entity->$name)){
        		continue;
        	}
        	$option = [
	            'class'=>$data['class']
	        ];
	        if(!$options['isNull']){
	            $option += ['required'=>''];
	        }
	        $type = 'text';        
	        switch($options['type']){
	            case 'bigint':
	            case 'int':
	                if(isset($options['key']) && $options['key'] === 'MUL'){
	                    $m = $relations[$name]['model'];                    
	                    $modelObj = new $m();
	                    $modelNameMap = $m::getPrefix().'_name';
	                    if($modelObj::getType($modelNameMap) === null){
	                        $modelNameMap = $name;
	                    }
	                    $type = 'Select';
	                    $args = $m::find();
	                    $option += [
	                    	'using' => [
	                            $relations[$name]['field'], 
	                            $modelNameMap
	                        ],
	                        'useEmpty' => true,
	                        'emptyText' => '-'
	                    ];	                   
	                } else if($primary === $name){
	                	$type = 'Hidden';
	                	$option = [];
	                } else {
	                    $type = 'Numeric';
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
	                $type = 'Numeric';
	                $option += [
	                    'min'=>0, 
	               /*     'max'=>((int)str_pad('1', $options['length'], '0')-1),
	                    'maxlength'=>$options['length'],
	                    'size'=>$options['length'],*/
	                    'step'=>'0.01'
	                ];
	                break;
	            case 'varchar':
	                $option += [
	                    'maxlength'=>$options['length'],
	                    'size'=>$options['length'],
	                ];
	                foreach($this->patterns as $pattern => $datas){
	                    if(preg_match("/_$pattern/", $name)){
	                        if($pattern === 'Email'){
	                            $type = 'Email';
	                        } else {
	                            if($pattern == 'password'){
	                            	$type='password';
	                            }
	                            $option += $datas;
	                        }
	                        break;
	                    }
	                }
	                if(str_replace($model::getPrefix().'_', '', $name) === 'email'){
	                	$type='Email';
	                }
	                break;  
	            case 'set':
	            case 'enum':
	                $type = 'Select';
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
	                $type = 'Select';
	                $args = [
	                    ''=>'-',
	                    '1'=>$t->_('yes'),
	                    '0'=>$t->_('no')
	                ];
	                break;  
	            case 'text':
	                $type = 'TextArea';
	                break;
	            case 'blob': 
	            case 'longblob':
	                $type = 'File';
	                break;
	            case 'datetime': 
	            case 'timestamp':
	                $type = 'DateTime';
	                break;
	            case 'date':
	                $type = 'Date';
	                break; 
	        }       
	        if(isset($data['autofocus']) && $data['autofocus'] === $name){
	        	$option['autofocus'] = '';	
	        }
	        $type = 'Phalcon\\Forms\\Element\\'.$type;
	        if(isset($args)){	            
	            $element = new $type($name, $args, $option);
	        } else {
	        	$element = new $type($name, $option);
	        }
	        $args = null;

	        $element->setLabel($t->_(str_replace('_id', '', $model::getColumnsMap()[$name]))."&nbsp;:&nbsp;");
	        $this->add($element);
        }
    }
}
