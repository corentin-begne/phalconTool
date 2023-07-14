<?
use Phalcon\FormBase;

/**
 * Manage [name] forms
 */
class [name]Form extends FormBase
{
    /**
	 * Inputs type patterns
	 * @var array
	 */
	protected array $patterns = [
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