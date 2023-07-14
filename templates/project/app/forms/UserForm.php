<?
use Phalcon\FormBase;

/**
 * Manage User forms
 */
class UserForm extends FormBase
{
    /**
	 * Inputs type patterns
	 * @var array
	 */
	protected array $patterns = [
        'password'=>[], 
        'email'=>[]
    ];

    /**
	 * Generate all elements of the instance [name] form
	 * 
	 * @param mixed $entity Model instance
	 * @param null|array $data=[] Params to use
	 * 
	 * @return void
	 */
    public function initialize(mixed $entity, null|array $data=[]):void{
        parent::initialize($entity, $data);
    }
}
