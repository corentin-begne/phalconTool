<?
    $object->add("/scrud/{model}/:action",[
        'controller' => 'scrud',
        'action'     => 2
    ]);
    $object->add("/scrud/{model}/", [
        'controller' => 'scrud',
        'action'     => 'index'
    ]);