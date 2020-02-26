<?
class [name] extends Phalcon\ModelBase
{
    [fields]
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
[constraints]
        $this->setSource('[realName]');
        parent::initialize();
    }

    /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
    public function columnMap()
    {
        return array(
            [maps]
        );
    }

}
