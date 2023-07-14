<?
namespace Phalcon;
use Phalcon\Forms\Form;

/**
 * Auto generate form elements based on a model
 */
class FormBase extends Form
{
	/**
	 * Inputs type patterns
	 * @var array
	 */
    protected array $patterns = [
        'phone'=>['pattern'=>'\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})'],
        'postal_code'=>['pattern'=>'^(([0-8][0-9])|(9[0-5]))[0-9]{3}$'],
        'password'=>[], 
        'email'=>[],
    ];

	/**
	 * Generate all elements of the instance model form
	 * 
	 * @param mixed $entity Model instance
	 * @param null|array $data=[] Params to use
	 * 
	 * @return void
	 */
    public function initialize(mixed $entity, null|array $data=[]):void{
    	$t = $this->translation;
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
			$option = [];
			if(isset($data['class'])){
				$option['class'] = $data['class'];
			}
	        if(!$options['nullable']){
	            $option['required'] = '';
	        }
	        $type = 'text';        
	        switch($options['mtype']){
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

	        $element->setLabel($t->_(str_replace('_id', '', $name)));
	        $this->add($element);
        }
    }
}
