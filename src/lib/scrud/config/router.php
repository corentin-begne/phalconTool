<?
    $object->add("/scrud/{model}/:action",[
        'controller' => 'scrud',
        'action'     => 2
    ]);
    $object->add("/scrud/{model}/", [
        'controller' => 'scrud',
        'action'     => 'search'
    ]);
    $object->add("/custom_scrud/{model}/:action",[
        'controller' => 'custom_scrud',
        'action'     => 2
    ]);
    $object->add("/custom_scrud/{model}/", [
        'controller' => 'custom_scrud',
        'action'     => 'search'
    ]);